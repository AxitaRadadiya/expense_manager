<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use App\Models\SubCategory;
use App\Models\Category;

class InvoiceController extends Controller
{
    public function index(): View
    {
        $invoices = Invoice::with(['customer','project','subCategory'])->latest()->get();
        return view('admin.invoice.index', compact('invoices'));
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
        $incomeCategory = Category::where('name','Income')->first();
        $incomeSubCategories = $incomeCategory ? SubCategory::where('category_id', $incomeCategory->id)->orderBy('name')->get() : collect();

        return view('admin.invoice.create', compact('customers','projects','incomeSubCategories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('sales-create')) {
            abort(403);
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'required|string',
            'status' => 'nullable|in:Paid,Pending',
            'invoice_date' => 'required|date|after_or_equal:today',
        ]);

        // ensure due_amount is initialized to the invoice amount
        $validated['due_amount'] = $validated['amount'];
        // default status to Pending when not provided by frontend
        if (empty($validated['status'])) { $validated['status'] = 'Pending'; }
        Invoice::create($validated);
        return redirect()->route('invoice.index')->with('success','Invoice created');
    }

    public function edit($id): View|\Illuminate\Http\RedirectResponse
    {
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('sales-edit')) {
            abort(403);
        }

        $invoice = Invoice::findOrFail($id);
        if ($invoice->status === 'Paid') {
            return redirect()->route('invoice.index')->with('error', 'Paid invoices cannot be edited');
        }
        $customerRoleId = \App\Models\Role::where('name','customer')->value('id');
        $customers = User::where('role_id', $customerRoleId)->orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        $incomeCategory = Category::where('name','Income')->first();
        $incomeSubCategories = $incomeCategory ? SubCategory::where('category_id', $incomeCategory->id)->orderBy('name')->get() : collect();

        return view('admin.invoice.edit', compact('invoice','customers','projects','incomeSubCategories'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('sales-edit')) {
            abort(403);
        }

        $invoice = Invoice::findOrFail($id);
        if ($invoice->status === 'Paid') {
            return redirect()->route('invoice.index')->with('error', 'Paid invoices cannot be edited');
        }
        $validated = $request->validate([
            'customer_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'required|string',
            'status' => 'nullable|in:Paid,Pending',
            'invoice_date' => 'required|date|after_or_equal:today',
        ]);
        // only update status if provided by the form (we hide it in frontend)
        if (!array_key_exists('status', $validated)) {
            unset($validated['status']);
        }
        $invoice->update($validated);

        // Recalculate due_amount after amount change using recorded payments
        $paid = (float) \DB::table('payment_receive_invoices')->where('invoice_id', $invoice->id)->sum('amount');
        $invoice->due_amount = max(0, (float)$invoice->amount - $paid);
        $invoice->status = $invoice->due_amount <= 0 ? 'Paid' : 'Pending';
        $invoice->save();
        return redirect()->route('invoice.index')->with('success','Invoice updated');
    }

    public function destroy($id): RedirectResponse
    {
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('sales-delete')) {
            abort(403);
        }

        $invoice = Invoice::findOrFail($id);
        $status = strtolower(trim($invoice->status ?? ''));
        if (in_array($status, ['paid', 'pais'])) {
            return redirect()->route('invoice.index')->with('error', 'Paid invoices cannot be deleted');
        }
        $invoice->delete();
        return redirect()->route('invoice.index')->with('success','Invoice deleted');
    }

    public function show($id): View
    {
        $invoice = Invoice::with(['customer','project','subCategory'])->findOrFail($id);
        return view('admin.invoice.show', compact('invoice'));
    }

    public function list(Request $request)
    {
        try {
            $columns = [0 => 'id', 1 => 'customer', 2 => 'project', 3 => 'category', 4 => 'amount', 5 => 'status', 6 => 'invoice_date', 7 => 'action'];

            $limit = intval($request->input('length', 10));
            $start = intval($request->input('start', 0));
            $orderColumnIndex = intval($request->input('order.0.column', 0));
            $order = $columns[$orderColumnIndex] ?? 'id';
            $dir = $request->input('order.0.dir', 'desc');
            $search = $request->input('search.value');

            $query = Invoice::with(['customer','project','subCategory']);

            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->whereHas('customer', function($q2) use ($search) { $q2->where('name', 'like', "%{$search}%"); })
                      ->orWhereHas('project', function($q2) use ($search) { $q2->where('name', 'like', "%{$search}%"); })
                      ->orWhereHas('subCategory', function($q2) use ($search) { $q2->where('name', 'like', "%{$search}%"); })
                      ->orWhere('amount', 'like', "%{$search}%");
                });
            }

            // optional filters
            if ($request->filled('customer_id')) {
                $query->where('customer_id', $request->input('customer_id'));
            }

            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }

            $totalData = Invoice::count();
            $totalFiltered = $query->count();

            // Order handling: only allow ordering by certain DB columns
            $allowedOrders = ['id','amount','invoice_date','status'];
            $orderBy = in_array($order, $allowedOrders) ? $order : 'id';

            $rows = $query->offset($start)
                ->limit($limit)
                ->orderBy($orderBy, $dir)
                ->get();

            // compute paid amounts per invoice from payment_receive_invoices (only for returned rows)
            $invoiceIds = $rows->pluck('id')->toArray();
            $paidRows = [];
            if (!empty($invoiceIds)) {
                $paidRows = \DB::table('payment_receive_invoices')
                    ->select('invoice_id', \DB::raw('IFNULL(SUM(amount),0) as paid'))
                    ->whereIn('invoice_id', $invoiceIds)
                    ->groupBy('invoice_id')
                    ->get()
                    ->keyBy('invoice_id')
                    ->map(function($r){ return (float)$r->paid; })
                    ->toArray();
            }

            $data = [];
            $i = $start + 1;
                foreach ($rows as $row) {
                $nested = [];
                $nested['id'] = $i;
                $nested['customer'] = $row->customer->name ?? '';
                $nested['project'] = $row->project->name ?? '';
                $nested['category'] = $row->subCategory->name ?? '';
                $nested['amount'] = $row->amount;
                $nested['status'] = $row->status;
                $nested['invoice_date'] = $row->invoice_date;

                $nested['project_id'] = $row->project_id;

                // include actual invoice id for client-side use
                $nested['invoice_id'] = $row->id;

                // compute paid and due amounts
                $paid = isset($paidRows[$row->id]) ? (float)$paidRows[$row->id] : 0.0;
                $nested['paid_amount'] = $paid;
                $nested['due_amount'] = max(0, (float)$row->amount - $paid);

                $actions = '<div class="btn-group">';
                $actions .= "<i class=\"fas fa-ellipsis-v\" data-toggle=\"dropdown\" style=\"cursor:pointer;\"></i>";
                $actions .= '<div class="dropdown-menu dropdown-menu-right" style="min-width: 50px; padding: 0;">';
                $auth = auth()->user();
                $canEdit = $auth?->hasPermission('sales-edit') ?? false;
                $canDelete = $auth?->hasPermission('sales-delete') ?? false;

                $actions .= '<a href="' . route('invoice.show', $row->id) . '" class="table-action-btn is-view" title="View"><i class="fa fa-eye"></i></a>';
                if ($canEdit) {
                    $actions .= '<a href="' . route('invoice.edit', $row->id) . '" class="table-action-btn is-edit" title="Edit"><i class="fa fa-edit"></i></a>';
                }
                if ($canDelete) {
                    $actions .= '<form action="' . route('invoice.destroy', $row->id) . '" method="POST" class="table-action-form">' . csrf_field() . '<input type="hidden" name="_method" value="DELETE">' . '<button type="button" class="table-action-btn is-delete deleteButton" title="Delete"><i class="fa fa-trash"></i></button></form>';
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
            \Log::error('Invoice list error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error', 'message' => $e->getMessage()], 500);
        }
    }
}
