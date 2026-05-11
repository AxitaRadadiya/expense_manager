<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\PaymentReceive;
use App\Models\Project;
use App\Models\User;

class PaymentReceiveController extends Controller
{
    public function index(): View
    {
        $payments = PaymentReceive::with(['customer','project'])->latest()->get();
        return view('admin.payment_receive.index', compact('payments'));
    }

    public function create(): View
    {
        $customerRoleId = \App\Models\Role::where('name','customer')->value('id');
        $customers = User::where('role_id', $customerRoleId)->orderBy('name')->get();
        $projects = Project::orderBy('name')->get();

        return view('admin.payment_receive.create', compact('customers','projects'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'payment_type' => 'required|in:cash,online,cheque',
            'customer_id' => 'nullable|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date|after_or_equal:today',
        ]);

        PaymentReceive::create($validated);
        return redirect()->route('payment-receive.index')->with('success','Payment recorded');
    }

    public function edit($id): View
    {
        $payment = PaymentReceive::findOrFail($id);
        $customerRoleId = \App\Models\Role::where('name','customer')->value('id');
        $customers = User::where('role_id', $customerRoleId)->orderBy('name')->get();
        $projects = Project::orderBy('name')->get();

        return view('admin.payment_receive.edit', compact('payment','customers','projects'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $payment = PaymentReceive::findOrFail($id);
        $validated = $request->validate([
            'payment_type' => 'required|in:cash,online,cheque',
            'customer_id' => 'nullable|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date|after_or_equal:today',
        ]);

        $payment->update($validated);
        return redirect()->route('payment-receive.index')->with('success','Payment updated');
    }

    public function destroy($id): RedirectResponse
    {
        $payment = PaymentReceive::findOrFail($id);
        $payment->delete();
        return redirect()->route('payment-receive.index')->with('success','Payment deleted');
    }

    public function list(Request $request)
    {
        try {
            $columns = [0 => 'id', 1 => 'payment_type', 2 => 'customer', 3 => 'project', 4 => 'amount', 5 => 'payment_date', 6 => 'action'];

            $limit = intval($request->input('length', 10));
            $start = intval($request->input('start', 0));
            $orderColumnIndex = intval($request->input('order.0.column', 0));
            $order = $columns[$orderColumnIndex] ?? 'id';
            $dir = $request->input('order.0.dir', 'desc');
            $search = $request->input('search.value');

            $query = PaymentReceive::with(['customer','project']);

            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('payment_type', 'like', "%{$search}%")
                      ->orWhereHas('customer', function($q2) use ($search) { $q2->where('name', 'like', "%{$search}%"); })
                      ->orWhereHas('project', function($q2) use ($search) { $q2->where('name', 'like', "%{$search}%"); })
                      ->orWhere('amount', 'like', "%{$search}%");
                });
            }

            $totalData = PaymentReceive::count();
            $totalFiltered = $query->count();

            $allowedOrders = ['id','amount','payment_date'];
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
                $nested['payment_type'] = ucfirst($row->payment_type);
                $nested['customer'] = $row->customer->name ?? '';
                $nested['project'] = $row->project->name ?? '';
                $nested['amount'] = $row->amount;
                $nested['payment_date'] = $row->payment_date;

                $actions = '<div class="btn-group">';
                $actions .= "<i class=\"fas fa-ellipsis-v\" data-toggle=\"dropdown\" style=\"cursor:pointer;\"></i>";
                $actions .= '<div class="dropdown-menu dropdown-menu-right" style="min-width: 50px; padding: 0;">';
                if (auth()->check()) {
                    $actions .= '<a href="' . route('payment-receive.edit', $row->id) . '" class="table-action-btn is-edit" title="Edit"><i class="fa fa-edit"></i></a>';
                    $actions .= '<form action="' . route('payment-receive.destroy', $row->id) . '" method="POST" class="table-action-form">' . csrf_field() . '<input type="hidden" name="_method" value="DELETE">' . '<button type="button" class="table-action-btn is-delete deleteButton" title="Delete"><i class="fa fa-trash"></i></button></form>';
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
            \Log::error('PaymentReceive list error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error', 'message' => $e->getMessage()], 500);
        }
    }
}
