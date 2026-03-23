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

        // Only allow super-admin and owner roles to view transfers index
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
            'user_id' => 'nullable|exists:users,id',
            'start_date' => 'nullable|date',
            'amount' => 'required|numeric',
        ]);

        $data['created_by'] = auth()->id();

        $transfer = Transfer::create($data);

        return redirect()->route('transfer.index')->with('success', 'Transfer saved successfully.');
    }

    public function list(Request $request)
    {
        $auth = auth()->user();

        // Only super-admin sees all transfers; owners see only their own
        if (! $auth || ! (method_exists($auth, 'hasRole') && $auth->hasRole(['super-admin', 'owner']))) {
            return response()->json([
                'draw' => intval($request->input('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ]);
        }

        $query = Transfer::with('user', 'creator')->latest();

        if ($auth->hasRole('owner') && ! $auth->hasRole('super-admin')) {
            $query->where('user_id', $auth->id());
        }

        // Search
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhere('amount', 'like', "%{$search}%");
        }

        $totalFiltered = $query->count();
        $totalData     = Transfer::count();

        $transfers = $query
            ->offset($request->input('start', 0))
            ->limit($request->input('length', 10))
            ->get();

        $data = $transfers->map(function ($transfer, $i) use ($request) {


            return [
                'id'         => $request->input('start', 0) + $i + 1,
                'user'       => '<strong>'.e($transfer->user->name ?? '—').'</strong>'
                            . '<br><small class="text-muted">by '.e($transfer->creator->name ?? '—').'</small>',
                'start_date' => $transfer->start_date
                                ? $transfer->start_date->format('d M Y')
                                : '—',
                'amount'     => '<span class="text-success font-weight-bold">₹'
                                . number_format($transfer->amount, 2) . '</span>',
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
