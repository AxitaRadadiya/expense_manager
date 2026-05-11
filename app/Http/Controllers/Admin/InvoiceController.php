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
        $customerRoleId = \App\Models\Role::where('name','customer')->value('id');
        $customers = User::where('role_id', $customerRoleId)->orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        $incomeCategory = Category::where('name','Income')->first();
        $incomeSubCategories = $incomeCategory ? SubCategory::where('category_id', $incomeCategory->id)->orderBy('name')->get() : collect();

        return view('admin.invoice.create', compact('customers','projects','incomeSubCategories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'required|string',
            'invoice_date' => 'required|date|after_or_equal:today',
        ]);

        Invoice::create($validated);
        return redirect()->route('invoice.index')->with('success','Invoice created');
    }

    public function edit($id): View
    {
        $invoice = Invoice::findOrFail($id);
        $customerRoleId = \App\Models\Role::where('name','customer')->value('id');
        $customers = User::where('role_id', $customerRoleId)->orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        $incomeCategory = Category::where('name','Income')->first();
        $incomeSubCategories = $incomeCategory ? SubCategory::where('category_id', $incomeCategory->id)->orderBy('name')->get() : collect();

        return view('admin.invoice.edit', compact('invoice','customers','projects','incomeSubCategories'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $invoice = Invoice::findOrFail($id);
        $validated = $request->validate([
            'customer_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'required|string',
            'invoice_date' => 'required|date|after_or_equal:today',
        ]);

        $invoice->update($validated);
        return redirect()->route('invoice.index')->with('success','Invoice updated');
    }

    public function destroy($id): RedirectResponse
    {
        $invoice = Invoice::findOrFail($id);
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
            $columns = [0 => 'id', 1 => 'customer', 2 => 'project', 3 => 'category', 4 => 'amount', 5 => 'invoice_date', 6 => 'action'];

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

            $totalData = Invoice::count();
            $totalFiltered = $query->count();

            // Order handling: only allow ordering by certain DB columns
            $allowedOrders = ['id','amount','invoice_date'];
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
                $nested['customer'] = $row->customer->name ?? '';
                $nested['project'] = $row->project->name ?? '';
                $nested['category'] = $row->subCategory->name ?? '';
                $nested['amount'] = $row->amount;
                $nested['invoice_date'] = $row->invoice_date;

                $actions = '<div class="btn-group">';
                $actions .= "<i class=\"fas fa-ellipsis-v\" data-toggle=\"dropdown\" style=\"cursor:pointer;\"></i>";
                $actions .= '<div class="dropdown-menu dropdown-menu-right" style="min-width: 50px; padding: 0;">';
                if (auth()->check()) {
                    $actions .= '<a href="' . route('invoice.show', $row->id) . '" class="table-action-btn is-view" title="View"><i class="fa fa-eye"></i></a>';
                    $actions .= '<a href="' . route('invoice.edit', $row->id) . '" class="table-action-btn is-edit" title="Edit"><i class="fa fa-edit"></i></a>';
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
