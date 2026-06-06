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
use App\Models\Item;
use App\Models\InvoiceItem;

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
        $items = Item::orderBy('name')->get();
        
        return view('admin.invoice.create', compact('customers','projects','incomeSubCategories','items'));
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
            'note' => 'required|string',
            'status' => 'nullable|in:Paid,Pending',
            'invoice_date' => 'required|date|after_or_equal:today',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'nullable|exists:items,id',
            'items.*.sub_category_id' => 'required|exists:sub_categories,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.unit_amount' => 'required|numeric|min:0',
        ]);

        // compute total amount from items
        $totalAmount = 0.0;
        foreach ($validated['items'] as $it) {
            $totalAmount += (float)$it['qty'] * (float)$it['unit_amount'];
        }

        $invoiceData = [
            'customer_id' => $validated['customer_id'],
            'project_id' => $validated['project_id'],
            'amount' => $totalAmount,
            'due_amount' => $totalAmount,
            'note' => $validated['note'],
            'invoice_date' => $validated['invoice_date'],
            'status' => $validated['status'] ?? 'Pending',
        ];

        // set sub_category_id of the invoice to first item sub_category if possible
        $firstSub = $validated['items'][0]['sub_category_id'] ?? null;
        if ($firstSub) { 
            $invoiceData['sub_category_id'] = $firstSub; 
        }

        $invoice = Invoice::create($invoiceData);

        // create invoice items
        foreach ($validated['items'] as $it) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'item_id' => $it['item_id'] ?? null,
                'sub_category_id' => $it['sub_category_id'],
                'qty' => $it['qty'],
                'unit_amount' => $it['unit_amount'],
                'total_amount' => (float)$it['qty'] * (float)$it['unit_amount'],
            ]);
        }

        return redirect()->route('invoice.index')->with('success','Invoice created');
    }

    public function edit($id): View|\Illuminate\Http\RedirectResponse
    {
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('sales-edit')) {
            abort(403);
        }

        // Load invoice with its items relationship
        $invoice = Invoice::with('invoiceItems')->findOrFail($id);
        
        if ($invoice->status === 'Paid') {
            return redirect()->route('invoice.index')->with('error', 'Paid invoices cannot be edited');
        }
        
        $customerRoleId = \App\Models\Role::where('name','customer')->value('id');
        $customers = User::where('role_id', $customerRoleId)->orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        $incomeCategory = Category::where('name','Income')->first();
        $incomeSubCategories = $incomeCategory ? SubCategory::where('category_id', $incomeCategory->id)->orderBy('name')->get() : collect();
        
        // Get all items for dropdown
        $items = Item::orderBy('name')->get();

        return view('admin.invoice.edit', compact('invoice', 'customers', 'projects', 'incomeSubCategories', 'items'));
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
            'note' => 'required|string',
            'status' => 'nullable|in:Paid,Pending',
            'invoice_date' => 'required|date|after_or_equal:today',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'nullable|exists:items,id',
            'items.*.sub_category_id' => 'required|exists:sub_categories,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.unit_amount' => 'required|numeric|min:0',
        ]);

        // compute total
        $totalAmount = 0.0;
        foreach ($validated['items'] as $it) {
            $totalAmount += (float)$it['qty'] * (float)$it['unit_amount'];
        }

        // update invoice core fields
        $invoice->customer_id = $validated['customer_id'];
        $invoice->project_id = $validated['project_id'];
        $invoice->amount = $totalAmount;
        $invoice->note = $validated['note'];
        $invoice->invoice_date = $validated['invoice_date'];
        if (array_key_exists('status', $validated)) { 
            $invoice->status = $validated['status']; 
        }
        $invoice->sub_category_id = $validated['items'][0]['sub_category_id'] ?? $invoice->sub_category_id;
        $invoice->save();

        // replace invoice items
        $invoice->invoiceItems()->delete();
        foreach ($validated['items'] as $it) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'item_id' => $it['item_id'] ?? null,
                'sub_category_id' => $it['sub_category_id'],
                'qty' => $it['qty'],
                'unit_amount' => $it['unit_amount'],
                'total_amount' => (float)$it['qty'] * (float)$it['unit_amount'],
            ]);
        }

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
        $invoice = Invoice::with(['customer','project','subCategory', 'invoiceItems'])->findOrFail($id);
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
                    $q->whereHas('customer', function($q2) use ($search) { 
                        $q2->where('name', 'like', "%{$search}%"); 
                    })
                      ->orWhereHas('project', function($q2) use ($search) { 
                        $q2->where('name', 'like', "%{$search}%"); 
                      })
                      ->orWhereHas('subCategory', function($q2) use ($search) { 
                        $q2->where('name', 'like', "%{$search}%"); 
                      })
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

            // Filter by specific invoice IDs (used when editing payments to load allocated invoices)
            if ($request->filled('invoice_ids')) {
                $invoiceIds = json_decode($request->input('invoice_ids'), true);
                if (is_array($invoiceIds) && !empty($invoiceIds)) {
                    $query->whereIn('id', $invoiceIds);
                }
            }

            // optional date range filter
            if ($request->filled('date_from')) {
                $query->whereDate('invoice_date', '>=', $request->input('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->whereDate('invoice_date', '<=', $request->input('date_to'));
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