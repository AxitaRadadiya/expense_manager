<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Transfer;
use App\Models\User;
use App\Models\UserBalanceHistory;
use App\Services\BalanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    public function __construct(protected BalanceService $balanceService)
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $auth = auth()->user();

        if (! $auth || ! (method_exists($auth, 'hasRole') && $auth->hasRole(['super-admin', 'owner']))) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized to view transfers.');
        }

        $transfers = Transfer::with(['user', 'creator'])->latest()->get();

        return view('admin.transfer.index', compact('transfers'));
    }

    public function create()
    {
        $auth = auth()->user();
        $usersQuery = User::orderBy('name');
        $assignedProjectIds = $auth ? $auth->assignedProjectIds() : [];

        // If the authenticated user is not a super-admin, limit the dropdown
        // to users who belong to any of the same projects as the auth user.
        if ($auth && (! method_exists($auth, 'hasRole') || ! $auth->hasRole('super-admin'))) {
            if (! empty($assignedProjectIds)) {
                $usersQuery->where(function ($q) use ($assignedProjectIds) {
                    $q->whereHas('projects', function ($query) use ($assignedProjectIds) {
                        $query->whereIn('projects.id', $assignedProjectIds);
                    })->orWhereIn('project_id', $assignedProjectIds);
                });
            } else {
                // No assigned projects — return empty set
                $usersQuery->whereRaw('1 = 0');
            }
        }

        // Exclude the current user from the recipient dropdown
        if ($auth) {
            $usersQuery->where('id', '<>', $auth->id);
        }

        $users = $usersQuery->get();

        return view('admin.transfer.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_date' => 'nullable|date',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string',
        ]);

        $sender = auth()->user();

        if (! $sender) {
            return redirect()->back()->withErrors(['auth' => 'User must be authenticated.'])->withInput();
        }

        if ((int) $data['user_id'] === (int) $sender->id) {
            return redirect()->back()->withErrors(['user_id' => 'You cannot transfer amount to yourself.'])->withInput();
        }

        if (method_exists($sender, 'hasRole') && $sender->hasRole('owner') && ! $sender->hasRole('super-admin')) {
            $target = User::find($data['user_id']);
            $sharedProjects = $target
                ? array_intersect($sender->assignedProjectIds(), $target->assignedProjectIds())
                : [];

            if (! $target || empty($sharedProjects)) {
                return redirect()->back()->withInput()->with('error', 'You can only create transfers for users in your project.');
            }
        }

        $recipient = User::findOrFail($data['user_id']);
        $this->balanceService->createTransfer($sender, $recipient, $data);

        return redirect()->route('transfer.index')
            ->with('success', 'Transfer saved successfully.');
    }

    public function list(Request $request)
    {
        $auth = auth()->user();

        if (! $auth || ! (method_exists($auth, 'hasRole') && $auth->hasRole(['super-admin', 'owner']))) {
            return response()->json([
                'draw' => intval($request->input('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ]);
        }

        $query = Transfer::with('user', 'creator');

        if ($auth->hasRole('owner') && ! $auth->hasRole('super-admin')) {
            $assignedProjectIds = $auth->assignedProjectIds();

            $query->whereHas('user', function ($q) use ($assignedProjectIds) {
                $q->whereHas('projects', function ($projectQuery) use ($assignedProjectIds) {
                    $projectQuery->whereIn('projects.id', $assignedProjectIds);
                });
            });
        }

        if (! empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                })->orWhere('amount', 'like', "%{$search}%")
                    ->orWhere('note', 'like', "%{$search}%");
            });
        }

        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc') === 'asc' ? 'asc' : 'desc';
        $columns = ['id', 'user', 'start_date', 'note', 'amount'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';

        if (in_array($orderColumn, ['id', 'start_date', 'amount'], true)) {
            $query->orderBy($orderColumn, $orderDir);
        } else {
            $query->latest();
        }

        $totalData = Transfer::count();
        $totalFiltered = $query->count();

        $transfers = $query
            ->offset((int) $request->input('start', 0))
            ->limit((int) $request->input('length', 10))
            ->get();

        $start = (int) $request->input('start', 0);

        $data = $transfers->map(function ($transfer, $i) use ($start) {
            if ($transfer->start_date) {
                try {
                    $date = $transfer->start_date instanceof \Carbon\Carbon
                        ? $transfer->start_date
                        : \Carbon\Carbon::parse($transfer->start_date);
                    $formattedDate = $date->format('d M Y');
                } catch (\Exception $e) {
                    $formattedDate = '-';
                }
            } else {
                $formattedDate = '-';
            }

            return [
                'id' => $start + $i + 1,
                'user' => '<strong>' . e($transfer->user->name ?? '-') . '</strong>'
                    . '<br><small class="text-muted">by ' . e($transfer->creator->name ?? '-') . '</small>',
                'start_date' => $formattedDate,
                'note' => e(filled($transfer->note) ? $transfer->note : '-'),
                'amount' => '<span class="text-success font-weight-bold">₹'
                    . number_format((float) $transfer->amount, 2) . '</span>',
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw', 1)),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }
}
