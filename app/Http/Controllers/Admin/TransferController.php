<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transfer;
use App\Models\User;
use App\Models\UserBalanceHistory;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    public function __construct()
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

        // If the current user is an owner (not super-admin), only show users in the same project
        if ($auth && method_exists($auth, 'hasRole') && $auth->hasRole('owner') && ! $auth->hasRole('super-admin')) {
            $usersQuery->where('project_id', $auth->project_id);
        }

        $users = $usersQuery->get();
        return view('admin.transfer.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'    => 'nullable|exists:users,id',
            'start_date' => 'nullable|date',
            'amount'     => 'required|numeric',
        ]);

        $data['created_by'] = auth()->id();

        // If the creator is an owner (not super-admin), enforce that the target user belongs to the same project
        $auth = auth()->user();
        if ($data['user_id'] && $auth && method_exists($auth, 'hasRole') && $auth->hasRole('owner') && ! $auth->hasRole('super-admin')) {
            $target = User::find($data['user_id']);
            if (! $target || (string)$target->project_id !== (string)$auth->project_id) {
                return redirect()->back()->withInput()->with('error', 'You can only create transfers for users in your project.');
            }
        }

        // Wrap in transaction: create transfer, update user balance and add history
        DB::transaction(function () use ($data) {
            $transfer = Transfer::create($data);

            if (! empty($data['user_id'])) {
                $user = User::find($data['user_id']);
                if ($user) {
                    $before = (float) $user->amount;
                    $after = $before + (float) $data['amount'];
                    $user->amount = $after;
                    $user->save();

                    UserBalanceHistory::create([
                        'user_id' => $user->id,
                        'change_type' => 'transfer',
                        'change_amount' => $data['amount'],
                        'balance_before' => $before,
                        'balance_after' => $after,
                        'reference_type' => 'transfer',
                        'reference_id' => $transfer->id,
                        'created_by' => auth()->id(),
                        'note' => 'Transfer created',
                    ]);
                }
            }
        });

        return redirect()->route('transfer.index')->with('success', 'Transfer saved successfully.');
    }

    public function list(Request $request)
    {
        $auth = auth()->user();

        if (! $auth || ! (method_exists($auth, 'hasRole') && $auth->hasRole(['super-admin', 'owner']))) {
            return response()->json([
                'draw'            => intval($request->input('draw', 1)),
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
            ]);
        }

        $query = Transfer::with('user', 'creator');

        // Owner sees only their own transfers
        if ($auth->hasRole('owner') && ! $auth->hasRole('super-admin')) {
            // owners should see transfers for users in their same project
            $query->whereHas('user', function ($q) use ($auth) {
                $q->where('project_id', $auth->project_id);
            });
        }

        // FIX: Wrap orWhere in a grouped closure to avoid breaking other conditions
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                })->orWhere('amount', 'like', "%{$search}%");
            });
        }

        // Ordering
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDir         = $request->input('order.0.dir', 'desc') === 'asc' ? 'asc' : 'desc';
        $columns          = ['id', 'user', 'start_date', 'amount'];
        $orderColumn      = $columns[$orderColumnIndex] ?? 'id';

        // Only order by real DB columns (skip 'user' relation column)
        if (in_array($orderColumn, ['id', 'start_date', 'amount'])) {
            $query->orderBy($orderColumn, $orderDir);
        } else {
            $query->latest();
        }

        $totalData     = Transfer::count();
        $totalFiltered = $query->count();

        $transfers = $query
            ->offset((int) $request->input('start', 0))
            ->limit((int) $request->input('length', 10))
            ->get();

        $start = (int) $request->input('start', 0);

        $data = $transfers->map(function ($transfer, $i) use ($start) {

            // FIX: Safely format start_date — handle both string and Carbon instances
            if ($transfer->start_date) {
                try {
                    $date = $transfer->start_date instanceof \Carbon\Carbon
                        ? $transfer->start_date
                        : \Carbon\Carbon::parse($transfer->start_date);
                    $formattedDate = $date->format('d M Y');
                } catch (\Exception $e) {
                    $formattedDate = '—';
                }
            } else {
                $formattedDate = '—';
            }

            return [
                'id'         => $start + $i + 1,
                'user'       => '<strong>' . e($transfer->user->name ?? '—') . '</strong>'
                              . '<br><small class="text-muted">by ' . e($transfer->creator->name ?? '—') . '</small>',
                'start_date' => $formattedDate,
                'amount'     => '<span class="text-success font-weight-bold">₹'
                              . number_format((float) $transfer->amount, 2) . '</span>',
            ];
        });

        return response()->json([
            'draw'            => intval($request->input('draw', 1)),
            'recordsTotal'    => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data'            => $data,
        ]);
    }
}