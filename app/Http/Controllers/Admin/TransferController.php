<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transfer;
use App\Models\User;
use App\Services\TransferService;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    public function __construct(protected TransferService $transferService)
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $auth = auth()->user();

        if (! $auth) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized to view transfers.');
        }

        $canViewAllTransfers = method_exists($auth, 'hasRole') && $auth->hasRole('super-admin');

        return view('admin.transfer.index', compact('canViewAllTransfers'));
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
        $this->transferService->createTransfer($sender, $recipient, $data);

        return redirect()->route('transfer.index')
            ->with('success', 'Transfer saved successfully.');
    }

    public function list(Request $request)
    {
        $auth = auth()->user();

        if (! $auth) {
            return response()->json([
                'draw' => intval($request->input('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ]);
        }

        $query = Transfer::with('user', 'creator');
        $canViewAllTransfers = method_exists($auth, 'hasRole') && $auth->hasRole('super-admin');

        if (! $canViewAllTransfers) {
            $query->where('created_by', $auth->id);
        }

        $totalData = (clone $query)->count();

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
                    $formattedDate = $date->format('d-m-Y');
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
