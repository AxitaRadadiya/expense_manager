<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Payment;
use App\Models\User;

class PaymentController extends Controller
{
    public function index(): View
    {
        $payments = Payment::with(['vendor'])->latest()->get();
        return view('admin.payments.index', compact('payments'));
    }

    public function create(): View
    {
        $vendorRoleId = \App\Models\Role::where('name', 'vendor')->value('id');
        $vendors = User::where('role_id', $vendorRoleId)->orderBy('name')->get();
        $projects = \App\Models\Project::orderBy('name')->get();

        return view('admin.payments.create', compact('vendors','projects'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date|after_or_equal:today',
        ]);

        Payment::create($validated);

        return redirect()->route('payment.index')->with('success', 'Payment recorded');
    }

    public function edit($id): View
    {
        $payment = Payment::findOrFail($id);
        $vendorRoleId = \App\Models\Role::where('name', 'vendor')->value('id');
        $vendors = User::where('role_id', $vendorRoleId)->orderBy('name')->get();
        $projects = \App\Models\Project::orderBy('name')->get();

        return view('admin.payments.edit', compact('payment','vendors','projects'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $payment = Payment::findOrFail($id);
        $validated = $request->validate([
            'vendor_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date|after_or_equal:today',
        ]);

        $payment->update($validated);

        return redirect()->route('payment.index')->with('success', 'Payment updated');
    }

    public function destroy($id): RedirectResponse
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();
        return redirect()->route('payment.index')->with('success', 'Payment deleted');
    }

    public function show($id): View
    {
        $payment = Payment::with(['vendor', 'project'])->findOrFail($id);
        return view('admin.payments.show', compact('payment'));
    }

    public function list(Request $request)
    {
        try {
            $columns = [0 => 'id', 1 => 'vendor', 2 => 'project', 3 => 'amount', 4 => 'payment_date', 5 => 'action'];

            $limit = intval($request->input('length', 10));
            $start = intval($request->input('start', 0));
            $orderColumnIndex = intval($request->input('order.0.column', 0));
            $order = $columns[$orderColumnIndex] ?? 'id';
            $dir = $request->input('order.0.dir', 'desc');
            $search = $request->input('search.value');

            $query = Payment::with(['vendor', 'project']);

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('vendor', function ($q2) use ($search) { $q2->where('name', 'like', "%{$search}%"); })
                      ->orWhereHas('project', function ($q2) use ($search) { $q2->where('name', 'like', "%{$search}%"); })
                      ->orWhere('amount', 'like', "%{$search}%");
                });
            }

            $totalData = Payment::count();
            $totalFiltered = $query->count();

            $allowedOrders = ['id', 'amount', 'payment_date'];
            $orderBy = in_array($order, $allowedOrders) ? $order : 'id';

            $rows = $query->offset($start)
                ->limit($limit)
                ->orderBy($orderBy, $dir)
                ->get();

            $data = [];
            $i = $start + 1;
            foreach ($rows as $row) {
                $nested = [];
                $nested['id'] = $i;
                $nested['vendor'] = $row->vendor->name ?? '';
                $nested['project'] = $row->project->name ?? '';
                $nested['amount'] = $row->amount;
                $nested['payment_date'] = $row->payment_date;

                $actions = '<div class="btn-group">';
                $actions .= "<i class=\"fas fa-ellipsis-v\" data-toggle=\"dropdown\" style=\"cursor:pointer;\"></i>";
                $actions .= '<div class="dropdown-menu dropdown-menu-right" style="min-width: 50px; padding: 0;">';
                if (auth()->check()) {
                    $actions .= '<a href="' . route('payment.show', $row->id) . '" class="table-action-btn is-view" title="View"><i class="fa fa-eye"></i></a>';
                    $actions .= '<a href="' . route('payment.edit', $row->id) . '" class="table-action-btn is-edit" title="Edit"><i class="fa fa-edit"></i></a>';
                    $actions .= '<form action="' . route('payment.destroy', $row->id) . '" method="POST" class="table-action-form">' . csrf_field() . '<input type="hidden" name="_method" value="DELETE">' . '<button type="button" class="table-action-btn is-delete deleteButton" title="Delete"><i class="fa fa-trash"></i></button></form>';
                }
                $actions .= '</div></div>';

                $nested['action'] = $actions;
                $data[] = $nested;
                $i++;
            }

            $json_data = [
                'draw' => intval($request->input('draw')),
                'recordsTotal' => intval($totalData),
                'recordsFiltered' => intval($totalFiltered),
                'data' => $data,
            ];

            return response()->json($json_data);
        } catch (\Exception $e) {
            \Log::error('Payment list error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error', 'message' => $e->getMessage()], 500);
        }
    }
}
