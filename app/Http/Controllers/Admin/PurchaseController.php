<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Purchase;
use App\Models\Project;
use App\Models\User;
use App\Models\SubCategory;
use App\Models\Category;

class PurchaseController extends Controller
{
    public function index(): View
    {
        $purchases = Purchase::with(['vendor', 'project', 'subCategory'])->latest()->get();
        $payments = \App\Models\Payment::with(['vendor','project'])->latest()->get();
        return view('admin.purchase.index', compact('purchases','payments'));
    }

    public function create(): View
    {
        $vendorRoleId = \App\Models\Role::where('name', 'vendor')->value('id');
        $vendors = User::where('role_id', $vendorRoleId)->orderBy('name')->get();
        $projects = Project::orderBy('name')->get();

        $expenseCategory = Category::where('name', 'Expense')->first();
        $expenseSubCategories = $expenseCategory ? SubCategory::where('category_id', $expenseCategory->id)->orderBy('name')->get() : collect();

        return view('admin.purchase.create', compact('vendors', 'projects', 'expenseSubCategories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'quantity' => 'required|integer|min:1',
            'note' => 'required|string',
            'purchase_date' => 'required|date|after_or_equal:today',
        ]);

        Purchase::create($validated);

        return redirect()->route('purchase.index')->with('success', 'Purchase created successfully');
    }

    public function edit($id): View
    {
        $purchase = Purchase::findOrFail($id);
        $vendorRoleId = \App\Models\Role::where('name', 'vendor')->value('id');
        $vendors = User::where('role_id', $vendorRoleId)->orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        $expenseCategory = Category::where('name', 'Expense')->first();
        $expenseSubCategories = $expenseCategory ? SubCategory::where('category_id', $expenseCategory->id)->orderBy('name')->get() : collect();

        return view('admin.purchase.edit', compact('purchase','vendors','projects','expenseSubCategories'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $purchase = Purchase::findOrFail($id);

        $validated = $request->validate([
            'vendor_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'quantity' => 'required|integer|min:1',
            'note' => 'required|string',
            'purchase_date' => 'required|date|after_or_equal:today',
        ]);

        $purchase->update($validated);

        return redirect()->route('purchase.index')->with('success', 'Purchase updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        $purchase = Purchase::findOrFail($id);
        $purchase->delete();
        return redirect()->route('purchase.index')->with('success', 'Purchase deleted');
    }

    public function show($id): View
    {
        $purchase = Purchase::with(['vendor', 'project', 'subCategory'])->findOrFail($id);
        return view('admin.purchase.show', compact('purchase'));
    }

    public function list(Request $request)
    {
        try {
            $columns = [0 => 'id', 1 => 'vendor', 2 => 'project', 3 => 'sub_category', 4 => 'amount', 5 => 'quantity', 6 => 'purchase_date', 7 => 'action'];

            $limit = intval($request->input('length', 10));
            $start = intval($request->input('start', 0));
            $orderColumnIndex = intval($request->input('order.0.column', 0));
            $order = $columns[$orderColumnIndex] ?? 'id';
            $dir = $request->input('order.0.dir', 'desc');
            $search = $request->input('search.value');

            $query = Purchase::with(['vendor', 'project', 'subCategory']);

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('vendor', function ($q2) use ($search) { $q2->where('name', 'like', "%{$search}%"); })
                      ->orWhereHas('project', function ($q2) use ($search) { $q2->where('name', 'like', "%{$search}%"); })
                      ->orWhereHas('subCategory', function ($q2) use ($search) { $q2->where('name', 'like', "%{$search}%"); })
                      ->orWhere('amount', 'like', "%{$search}%")
                      ->orWhere('quantity', 'like', "%{$search}%");
                });
            }

            $totalData = Purchase::count();
            $totalFiltered = $query->count();

            $allowedOrders = ['id', 'amount', 'purchase_date', 'quantity'];
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
                $nested['sub_category'] = $row->subCategory->name ?? '';
                $nested['amount'] = $row->amount;
                $nested['quantity'] = $row->quantity;
                $nested['purchase_date'] = $row->purchase_date;

                $actions = '<div class="btn-group">';
                $actions .= "<i class=\"fas fa-ellipsis-v\" data-toggle=\"dropdown\" style=\"cursor:pointer;\"></i>";
                $actions .= '<div class="dropdown-menu dropdown-menu-right" style="min-width: 50px; padding: 0;">';
                if (auth()->check()) {
                    $actions .= '<a href="' . route('purchase.show', $row->id) . '" class="table-action-btn is-view" title="View"><i class="fa fa-eye"></i></a>';
                    $actions .= '<a href="' . route('purchase.edit', $row->id) . '" class="table-action-btn is-edit" title="Edit"><i class="fa fa-edit"></i></a>';
                    $actions .= '<form action="' . route('purchase.destroy', $row->id) . '" method="POST" class="table-action-form">' . csrf_field() . '<input type="hidden" name="_method" value="DELETE">' . '<button type="button" class="table-action-btn is-delete deleteButton" title="Delete"><i class="fa fa-trash"></i></button></form>';
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
            \Log::error('Purchase list error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error', 'message' => $e->getMessage()], 500);
        }
    }
}
