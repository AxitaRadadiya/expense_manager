<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transfer;
use App\Models\User;

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
        $users = User::orderBy('name')->get();
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

        Transfer::create($data);

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
            $query->where('user_id', $auth->id);
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