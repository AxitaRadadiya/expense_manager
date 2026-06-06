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
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('purchase-create')) {
            abort(403);
        }

        $vendorRoleId = \App\Models\Role::where('name', 'vendor')->value('id');
        $vendors = User::where('role_id', $vendorRoleId)->orderBy('name')->get();
        // project selection removed from payments create form
        return view('admin.payments.create', compact('vendors'));
    }

    public function store(Request $request): RedirectResponse
    {
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('purchase-create')) {
            abort(403);
        }

        $validated = $request->validate([
            'vendor_id' => 'required|exists:users,id',
            'project_id' => 'nullable|exists:projects,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date|after_or_equal:today',
        ]);

        // server-side guard: sum of allocations must not exceed payment amount
        $allocatedSum = 0;
        if ($request->has('purchase_payments') && is_array($request->purchase_payments)) {
            foreach ($request->purchase_payments as $v) { $allocatedSum += floatval($v); }
        }
        if ($allocatedSum > floatval($validated['amount'])) {
            return redirect()->back()->withInput()->withErrors(['amount' => 'Allocated amount exceeds total payment amount']);
        }

        // If purchase allocations exist, derive the payment's project_id from the first allocated purchase
        $derivedProjectId = $validated['project_id'] ?? null;
        if (empty($derivedProjectId) && $request->has('purchase_payments') && is_array($request->purchase_payments)) {
            foreach ($request->purchase_payments as $purchaseId => $payAmt) {
                $amt = floatval($payAmt);
                if ($amt <= 0) continue;
                $purchase = \App\Models\Purchase::find($purchaseId);
                if ($purchase && !empty($purchase->project_id)) {
                    $derivedProjectId = $purchase->project_id;
                    break;
                }
            }
        }

        $validated['project_id'] = $derivedProjectId;

        $payment = Payment::create($validated);

        // If allocations to specific purchases provided, create allocation records and apply them
        if ($request->has('purchase_payments') && is_array($request->purchase_payments)) {
            foreach ($request->purchase_payments as $purchaseId => $payAmt) {
                $amt = floatval($payAmt);
                if ($amt <= 0) continue;
                $purchase = \App\Models\Purchase::find($purchaseId);
                if (!$purchase) continue;

                // create allocation record
                \App\Models\PaymentAllocation::create([
                    'payment_id' => $payment->id,
                    'purchase_id' => $purchase->id,
                    'amount' => $amt,
                ]);

                // update purchase due_amount/status
                $currentDue = floatval($purchase->due_amount ?? $purchase->amount ?? 0);
                $newDue = max(0, $currentDue - $amt);
                $purchase->due_amount = $newDue;
                $purchase->status = ($newDue <= 0) ? 'paid' : 'pending';
                $purchase->save();
            }
        }

        return redirect()->route('payment.index')->with('success', 'Payment recorded');
    }

    public function edit($id): View
    {
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('purchase-edit')) {
            abort(403);
        }

        $payment = Payment::with(['allocations.purchase'])->findOrFail($id);
        $vendorRoleId = \App\Models\Role::where('name', 'vendor')->value('id');
        $vendors = User::where('role_id', $vendorRoleId)->orderBy('name')->get();
        // project selection removed from payments edit form
        return view('admin.payments.edit', compact('payment','vendors'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('purchase-edit')) {
            abort(403);
        }

        $payment = Payment::findOrFail($id);
        $validated = $request->validate([
            'vendor_id' => 'required|exists:users,id',
            'project_id' => 'nullable|exists:projects,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date|after_or_equal:today',
        ]);

        $payment->update($validated);

        // revert previous allocations (restore purchase due_amounts)
        $existingAllocs = $payment->allocations()->get();
        foreach ($existingAllocs as $alloc) {
            $purchase = \App\Models\Purchase::find($alloc->purchase_id);
            if ($purchase) {
                $currentDue = floatval($purchase->due_amount ?? $purchase->amount ?? 0);
                $purchase->due_amount = max(0, $currentDue + floatval($alloc->amount));
                $purchase->status = ($purchase->due_amount <= 0) ? 'paid' : 'pending';
                $purchase->save();
            }
        }
        // delete old allocations
        $payment->allocations()->delete();

        // apply new allocations if provided
        $allocatedSum = 0;
        if ($request->has('purchase_payments') && is_array($request->purchase_payments)) {
            foreach ($request->purchase_payments as $v) { $allocatedSum += floatval($v); }
        }
        if ($allocatedSum > floatval($validated['amount'])) {
            return redirect()->back()->withInput()->withErrors(['amount' => 'Allocated amount exceeds total payment amount']);
        }

        if ($request->has('purchase_payments') && is_array($request->purchase_payments)) {
            foreach ($request->purchase_payments as $purchaseId => $payAmt) {
                $amt = floatval($payAmt);
                if ($amt <= 0) continue;
                $purchase = \App\Models\Purchase::find($purchaseId);
                if (!$purchase) continue;

                \App\Models\PaymentAllocation::create([
                    'payment_id' => $payment->id,
                    'purchase_id' => $purchase->id,
                    'amount' => $amt,
                ]);

                $currentDue = floatval($purchase->due_amount ?? $purchase->amount ?? 0);
                $newDue = max(0, $currentDue - $amt);
                $purchase->due_amount = $newDue;
                $purchase->status = ($newDue <= 0) ? 'paid' : 'pending';
                $purchase->save();
            }
        }

        return redirect()->route('payment.index')->with('success', 'Payment updated');
    }

    public function destroy($id): RedirectResponse
    {
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('purchase-delete')) {
            abort(403);
        }

        $payment = Payment::findOrFail($id);
        $payment->delete();
        return redirect()->route('payment.index')->with('success', 'Payment deleted');
    }

    public function show($id): View
    {
        $payment = Payment::with(['vendor', 'project', 'allocations.purchase', 'allocations.purchase.project'])->findOrFail($id);
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Get purchases allocated to a specific payment for editing.
     * Returns both allocated purchases and pending purchases for the vendor.
     */
    public function getAllocatedPurchases($paymentId)
    {
        $payment = Payment::with(['allocations.purchase'])->findOrFail($paymentId);
        
        $allocatedPurchases = [];
        $allocatedPurchaseIds = [];
        
        foreach ($payment->allocations as $alloc) {
            $purchase = $alloc->purchase;
            if ($purchase) {
                $allocatedPurchases[] = [
                    'id' => $purchase->id,
                    'purchase_id' => $purchase->id,
                    'purchase_date' => $purchase->purchase_date,
                    'amount' => $purchase->amount,
                    'due_amount' => $purchase->due_amount,
                    'status' => ucfirst($purchase->status ?? ''),
                    'project' => $purchase->project->name ?? '-',
                    'project_id' => $purchase->project_id,
                    'vendor' => $purchase->vendor->name ?? '',
                    'allocated_amount' => $alloc->amount,
                ];
                $allocatedPurchaseIds[] = $purchase->id;
            }
        }
        
        return response()->json([
            'payment' => [
                'id' => $payment->id,
                'vendor_id' => $payment->vendor_id,
                'amount' => $payment->amount,
                'payment_date' => $payment->payment_date,
            ],
            'allocated_purchases' => $allocatedPurchases,
            'allocated_purchase_ids' => $allocatedPurchaseIds,
        ]);
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

            // optional date range filter
            if ($request->filled('date_from')) {
                $query->whereDate('payment_date', '>=', $request->input('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->whereDate('payment_date', '<=', $request->input('date_to'));
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
                $auth = auth()->user();
                $canEdit = $auth?->hasPermission('purchase-edit') ?? false;
                $canDelete = $auth?->hasPermission('purchase-delete') ?? false;

                $actions .= '<a href="' . route('payment.show', $row->id) . '" class="table-action-btn is-view" title="View"><i class="fa fa-eye"></i></a>';
                if ($canEdit) {
                    $actions .= '<a href="' . route('payment.edit', $row->id) . '" class="table-action-btn is-edit" title="Edit"><i class="fa fa-edit"></i></a>';
                }
                if ($canDelete) {
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
