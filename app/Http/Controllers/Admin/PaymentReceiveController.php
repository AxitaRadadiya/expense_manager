<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\PaymentReceive;
use App\Models\Project;
use App\Models\User;
use App\Models\Invoice;

class PaymentReceiveController extends Controller
{
    public function index(): View
    {
        $payments = PaymentReceive::with(['customer','project'])->latest()->get();
        return view('admin.payment_receive.index', compact('payments'));
    }

    public function create(): View
    {
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('sales-create')) {
            abort(403);
        }

        $customerRoleId = \App\Models\Role::where('name','customer')->value('id');
        $customers = User::where('role_id', $customerRoleId)->orderBy('name')->get();
        $projects = Project::orderBy('name')->get();

        return view('admin.payment_receive.create', compact('customers','projects'));
    }

    public function store(Request $request): RedirectResponse
    {
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('sales-create')) {
            abort(403);
        }

        $validated = $request->validate([
            'payment_type' => 'required|in:cash,online,cheque',
            'customer_id' => 'nullable|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date|after_or_equal:today',
        ]);

        $payment = PaymentReceive::create($validated);

        // store allocations to invoices if provided
        $invoicePayments = $request->input('invoice_payments', []);
        $affectedInvoiceIds = [];

        // remaining amount from the payment that can be allocated (leftover becomes extra_amount)
        $remainingPayment = (float) $payment->amount;
        if (!empty($invoicePayments) && is_array($invoicePayments)) {
            foreach ($invoicePayments as $invoiceId => $amt) {
                if ($remainingPayment <= 0) { break; }
                $amt = (float)$amt;
                // server-side clamp: do not allow payment > remaining due
                $invoice = Invoice::find((int)$invoiceId);
                if ($invoice) {
                    $alreadyPaid = (float) \DB::table('payment_receive_invoices')->where('invoice_id', $invoice->id)->sum('amount');
                    $due = max(0, (float)$invoice->amount - $alreadyPaid);
                    // clamp by due and remaining payment
                    $alloc = min($amt, $due, $remainingPayment);
                } else {
                    $alloc = min($amt, $remainingPayment);
                }
                if ($alloc > 0) {
                    \DB::table('payment_receive_invoices')->insert([
                        'payment_receive_id' => $payment->id,
                        'invoice_id' => (int)$invoiceId,
                        'amount' => $alloc,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $affectedInvoiceIds[] = (int)$invoiceId;
                    $remainingPayment -= $alloc;
                }
            }
        }

        // Update invoices' due_amount and status based on payments
        $affectedInvoiceIds = array_unique($affectedInvoiceIds);
        foreach ($affectedInvoiceIds as $invId) {
            $totalPaid = (float) \DB::table('payment_receive_invoices')->where('invoice_id', $invId)->sum('amount');
            $invoice = Invoice::find($invId);
            if ($invoice) {
                $due = max(0, (float)$invoice->amount - $totalPaid);
                $invoice->due_amount = $due;
                $invoice->status = $due <= 0 ? 'Paid' : 'Pending';
                $invoice->save();
            }
        }

        // If there's remaining (unallocated) amount and a customer is selected,
        // store it on the user's `extra_amount` field so it can be used later.
        if ($remainingPayment > 0 && !empty($payment->customer_id)) {
            $user = User::find($payment->customer_id);
            if ($user) {
                $current = (float) ($user->extra_amount ?? 0);
                $user->extra_amount = $current + $remainingPayment;
                $user->save();
            }
        }

        return redirect()->route('payment-receive.index')->with('success','Payment recorded');
    }

    public function edit($id): View
    {
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('sales-edit')) {
            abort(403);
        }

        $payment = PaymentReceive::findOrFail($id);
        $customerRoleId = \App\Models\Role::where('name','customer')->value('id');
        $customers = User::where('role_id', $customerRoleId)->orderBy('name')->get();
        $projects = Project::orderBy('name')->get();

        // fetch existing invoice allocations for this payment
        $allocations = \DB::table('payment_receive_invoices')
            ->where('payment_receive_id', $payment->id)
            ->pluck('amount', 'invoice_id')
            ->toArray();

        // Get the invoice IDs that are associated with this payment (for loading in edit screen)
        $allocatedInvoiceIds = array_keys($allocations);

        return view('admin.payment_receive.edit', compact('payment','customers','projects','allocations','allocatedInvoiceIds'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('sales-edit')) {
            abort(403);
        }

        $payment = PaymentReceive::findOrFail($id);
        $validated = $request->validate([
            'payment_type' => 'required|in:cash,online,cheque',
            'customer_id' => 'nullable|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date|after_or_equal:today',
        ]);

        // Get old allocations before updating
        $oldAllocations = \DB::table('payment_receive_invoices')
            ->where('payment_receive_id', $payment->id)
            ->pluck('amount', 'invoice_id')
            ->toArray();

        // Revert old allocations (restore invoice due_amounts)
        foreach ($oldAllocations as $invoiceId => $oldAmount) {
            $invoice = Invoice::find($invoiceId);
            if ($invoice) {
                $currentDue = floatval($invoice->due_amount ?? 0);
                $invoice->due_amount = max(0, $currentDue + floatval($oldAmount));
                $invoice->status = $invoice->due_amount <= 0 ? 'Paid' : 'Pending';
                $invoice->save();
            }
        }

        // Delete old allocations
        \DB::table('payment_receive_invoices')->where('payment_receive_id', $payment->id)->delete();

        // Update payment fields
        $payment->update($validated);

        // Apply new allocations
        $invoicePayments = $request->input('invoice_payments', []);
        $affectedInvoiceIds = [];
        $remainingPayment = (float) $payment->amount;

        if (!empty($invoicePayments) && is_array($invoicePayments)) {
            foreach ($invoicePayments as $invoiceId => $amt) {
                if ($remainingPayment <= 0) { break; }
                $amt = (float)$amt;
                if ($amt <= 0) continue;

                $invoice = Invoice::find((int)$invoiceId);
                if ($invoice) {
                    $alreadyPaid = (float) \DB::table('payment_receive_invoices')->where('invoice_id', $invoice->id)->sum('amount');
                    $due = max(0, (float)$invoice->amount - $alreadyPaid);
                    $alloc = min($amt, $due, $remainingPayment);
                } else {
                    $alloc = min($amt, $remainingPayment);
                }

                if ($alloc > 0) {
                    \DB::table('payment_receive_invoices')->insert([
                        'payment_receive_id' => $payment->id,
                        'invoice_id' => (int)$invoiceId,
                        'amount' => $alloc,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $affectedInvoiceIds[] = (int)$invoiceId;
                    $remainingPayment -= $alloc;
                }
            }
        }

        // Update invoices' due_amount and status
        $affectedInvoiceIds = array_unique($affectedInvoiceIds);
        foreach ($affectedInvoiceIds as $invId) {
            $totalPaid = (float) \DB::table('payment_receive_invoices')->where('invoice_id', $invId)->sum('amount');
            $invoice = Invoice::find($invId);
            if ($invoice) {
                $due = max(0, (float)$invoice->amount - $totalPaid);
                $invoice->due_amount = $due;
                $invoice->status = $due <= 0 ? 'Paid' : 'Pending';
                $invoice->save();
            }
        }

        // Handle remaining (unallocated) amount
        if ($remainingPayment > 0 && !empty($payment->customer_id)) {
            $user = User::find($payment->customer_id);
            if ($user) {
                $current = (float) ($user->extra_amount ?? 0);
                $user->extra_amount = $current + $remainingPayment;
                $user->save();
            }
        }

        return redirect()->route('payment-receive.index')->with('success','Payment updated');
    }

    public function destroy($id): RedirectResponse
    {
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('sales-delete')) {
            abort(403);
        }

        $payment = PaymentReceive::findOrFail($id);
        $payment->delete();
        return redirect()->route('payment-receive.index')->with('success','Payment deleted');
    }

    public function show($id): View
    {
        $payment = PaymentReceive::with(['customer','project'])->findOrFail($id);
        
        // Fetch existing invoice allocations for this payment
        $allocations = \DB::table('payment_receive_invoices')
            ->where('payment_receive_id', $payment->id)
            ->get()
            ->map(function($alloc) {
                $invoice = Invoice::find($alloc->invoice_id);
                return [
                    'invoice_id' => $alloc->invoice_id,
                    'invoice_no' => $invoice->invoice_no ?? '#'.$alloc->invoice_id,
                    'invoice_date' => $invoice->invoice_date ?? '-',
                    'project_name' => $invoice->project->name ?? '-',
                    'invoice_amount' => $invoice->amount ?? 0,
                    'allocated_amount' => $alloc->amount,
                ];
            });
        
        return view('admin.payment_receive.show', compact('payment', 'allocations'));
    }

    /**
     * Get invoices allocated to a specific payment for editing.
     * Returns both allocated invoices and pending invoices for the customer.
     */
    public function getAllocatedInvoices($paymentId)
    {
        $payment = PaymentReceive::with(['customer'])->findOrFail($paymentId);
        
        // Get existing allocations
        $allocations = \DB::table('payment_receive_invoices')
            ->where('payment_receive_id', $paymentId)
            ->get();
        
        $allocatedInvoices = [];
        $allocatedInvoiceIds = [];
        
        foreach ($allocations as $alloc) {
            $invoice = Invoice::find($alloc->invoice_id);
            if ($invoice) {
                $allocatedInvoices[] = [
                    'invoice_id' => $invoice->id,
                    'id' => $invoice->id,
                    'invoice_date' => $invoice->invoice_date,
                    'invoice_no' => $invoice->invoice_no,
                    'amount' => $invoice->amount,
                    'due_amount' => $invoice->due_amount,
                    'status' => $invoice->status ?? 'Pending',
                    'project' => ($invoice->project && $invoice->project->name) ? $invoice->project->name : '-',
                    'project_id' => $invoice->project_id,
                    'customer' => ($invoice->customer && $invoice->customer->name) ? $invoice->customer->name : '',
                    'allocated_amount' => $alloc->amount,
                ];
                $allocatedInvoiceIds[] = $invoice->id;
            }
        }
        
        return response()->json([
            'payment' => [
                'id' => $payment->id,
                'customer_id' => $payment->customer_id,
                'amount' => $payment->amount,
                'payment_date' => $payment->payment_date,
            ],
            'allocated_invoices' => $allocatedInvoices,
            'allocated_invoice_ids' => $allocatedInvoiceIds,
        ]);
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

            // optional date range filter
            if ($request->filled('date_from')) {
                $query->whereDate('payment_date', '>=', $request->input('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->whereDate('payment_date', '<=', $request->input('date_to'));
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
                $auth = auth()->user();
                $canEdit = $auth?->hasPermission('sales-edit') ?? false;
                $canDelete = $auth?->hasPermission('sales-delete') ?? false;

                $actions .= '<a href="' . route('payment-receive.show', $row->id) . '" class="table-action-btn is-view" title="View"><i class="fa fa-eye"></i></a>';
                if ($canEdit) {
                    $actions .= '<a href="' . route('payment-receive.edit', $row->id) . '" class="table-action-btn is-edit" title="Edit"><i class="fa fa-edit"></i></a>';
                }
                if ($canDelete) {
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
