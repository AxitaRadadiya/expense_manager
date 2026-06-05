<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Project;
use App\Models\User;
use App\Models\SubCategory;
use App\Models\Category;
use App\Models\Item;
use Carbon\Carbon;
use App\Models\PurchaseLabour;

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
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('purchase-create')) {
            abort(403);
        }

        $vendorRoleId = \App\Models\Role::where('name', 'vendor')->value('id');
        $vendors = User::where('role_id', $vendorRoleId)->orderBy('name')->get();
        $projects = Project::orderBy('name')->get();

        $expenseCategory = Category::where('name', 'Expense')->first();
        $expenseSubCategories = $expenseCategory ? SubCategory::where('category_id', $expenseCategory->id)->orderBy('name')->get() : collect();

        $items = Item::orderBy('name')->get();
        return view('admin.purchase.create', compact('vendors', 'projects', 'expenseSubCategories', 'items'));
    }

    public function store(Request $request): RedirectResponse
    {
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('purchase-create')) {
            abort(403);
        }

        $validated = $request->validate([
            'vendor_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string',
            'purchase_date' => 'required|date|after_or_equal:today',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'items' => 'nullable|array',
            'items.*.sub_category_id' => 'required_with:items|exists:sub_categories,id',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('purchases', 'public');
            $validated['image'] = $imagePath;
        }

        // set due_amount and status on create
        $validated['due_amount'] = $validated['amount'];
        $validated['status'] = ($validated['amount'] <= 0) ? 'paid' : 'pending';

        // set purchase sub_category_id from first item's sub_category if provided
        if ($request->has('items') && is_array($request->items) && !empty($request->items[0]['sub_category_id'])) {
            $validated['sub_category_id'] = $request->items[0]['sub_category_id'];
        }

        $purchase = Purchase::create($validated);

        // If items array provided, create purchase_items entries
        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $it) {
                $quantity = intval($it['quantity'] ?? ($it['qty'] ?? 1));
                $amount = floatval($it['amount'] ?? 0);
                $days = 1;
                if (!empty($it['date_start']) && !empty($it['date_end'])) {
                    try {
                        $days = Carbon::parse($it['date_start'])->diffInDays(Carbon::parse($it['date_end'])) + 1;
                        if ($days < 1) $days = 1;
                    } catch (\Exception $e) {
                        $days = 1;
                    }
                }
                $data = [
                    'purchase_id' => $purchase->id,
                    'item_id' => $it['item_id'] ?? null,
                    'sub_category_id' => $it['sub_category_id'] ?? null,
                    'quantity' => $quantity,
                    'date_start' => $it['date_start'] ?? null,
                    'date_end' => $it['date_end'] ?? null,
                    'amount' => $amount,
                    'total_amount' => round($quantity * $amount * $days, 2),
                    'note' => $it['note'] ?? null,
                ];
                PurchaseItem::create($data);
            }
        }

        // If labours array provided, create purchase_labours entries
        if ($request->has('labours') && is_array($request->labours)) {
            foreach ($request->labours as $lb) {
                $numbers = intval($lb['numbers'] ?? 1);
                $amount = floatval($lb['amount'] ?? 0);
                $days = 1;
                if (!empty($lb['date_start']) && !empty($lb['date_end'])) {
                    try {
                        $days = Carbon::parse($lb['date_start'])->diffInDays(Carbon::parse($lb['date_end'])) + 1;
                        if ($days < 1) $days = 1;
                    } catch (\Exception $e) { $days = 1; }
                }
                $ldata = [
                    'purchase_id' => $purchase->id,
                    'labour' => $lb['labour'] ?? null,
                    'numbers' => $numbers,
                    'date_start' => $lb['date_start'] ?? null,
                    'date_end' => $lb['date_end'] ?? null,
                    'amount' => $amount,
                    'total_amount' => round($numbers * $amount * $days, 2),
                    'note' => $lb['note'] ?? null,
                ];
                PurchaseLabour::create($ldata);
            }
        }

        return redirect()->route('purchase.index')->with('success', 'Purchase created successfully');
    }

    public function edit($id): View|\Illuminate\Http\RedirectResponse
    {
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('purchase-edit')) {
            abort(403);
        }

        $purchase = Purchase::findOrFail($id);
        if (strtolower($purchase->status) === 'paid') {
            return redirect()->route('purchase.index')->with('error', 'Paid purchases cannot be edited');
        }
        $vendorRoleId = \App\Models\Role::where('name', 'vendor')->value('id');
        $vendors = User::where('role_id', $vendorRoleId)->orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        $expenseCategory = Category::where('name', 'Expense')->first();
        $expenseSubCategories = $expenseCategory ? SubCategory::where('category_id', $expenseCategory->id)->orderBy('name')->get() : collect();

        $items = Item::orderBy('name')->get();
        return view('admin.purchase.edit', compact('purchase','vendors','projects','expenseSubCategories','items'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('purchase-edit')) {
            abort(403);
        }

        $purchase = Purchase::findOrFail($id);
        if (strtolower($purchase->status) === 'paid') {
            return redirect()->route('purchase.index')->with('error', 'Paid purchases cannot be edited');
        }

        $validated = $request->validate([
            'vendor_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string',
            'purchase_date' => 'required|date|after_or_equal:today',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'items' => 'nullable|array',
            'items.*.sub_category_id' => 'required_with:items|exists:sub_categories,id',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($purchase->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($purchase->image);
            }
            $imagePath = $request->file('image')->store('purchases', 'public');
            $validated['image'] = $imagePath;
        }

        // preserve existing payments when amount changes
        $oldAmount = $purchase->amount;
        $oldDue = $purchase->due_amount ?? $oldAmount;
        $delta = $validated['amount'] - $oldAmount;
        $newDue = max(0, ($oldDue) + $delta);
        $validated['due_amount'] = $newDue;
        $validated['status'] = ($newDue <= 0) ? 'paid' : 'pending';

        $purchase->update($validated);

        // set purchase sub_category_id to first item's sub_category if provided
        if ($request->has('items') && is_array($request->items) && !empty($request->items[0]['sub_category_id'])) {
            $purchase->sub_category_id = $request->items[0]['sub_category_id'];
            $purchase->save();
        }

        // Update purchase items if provided
        if ($request->has('items') && is_array($request->items)) {
            // remove existing items
            $purchase->purchaseItems()->delete();
            foreach ($request->items as $it) {
                $quantity = intval($it['quantity'] ?? ($it['qty'] ?? 1));
                $amount = floatval($it['amount'] ?? 0);
                $days = 1;
                if (!empty($it['date_start']) && !empty($it['date_end'])) {
                    try {
                        $days = Carbon::parse($it['date_start'])->diffInDays(Carbon::parse($it['date_end'])) + 1;
                        if ($days < 1) $days = 1;
                    } catch (\Exception $e) {
                        $days = 1;
                    }
                }
                $data = [
                    'purchase_id' => $purchase->id,
                    'item_id' => $it['item_id'] ?? null,
                    'sub_category_id' => $it['sub_category_id'] ?? null,
                    'quantity' => $quantity,
                    'date_start' => $it['date_start'] ?? null,
                    'date_end' => $it['date_end'] ?? null,
                    'amount' => $amount,
                    'total_amount' => round($quantity * $amount * $days, 2),
                    'note' => $it['note'] ?? null,
                ];
                PurchaseItem::create($data);
            }
        }

        // Update purchase labours if provided
        if ($request->has('labours') && is_array($request->labours)) {
            $purchase->purchaseLabours()->delete();
            foreach ($request->labours as $lb) {
                $numbers = intval($lb['numbers'] ?? 1);
                $amount = floatval($lb['amount'] ?? 0);
                $days = 1;
                if (!empty($lb['date_start']) && !empty($lb['date_end'])) {
                    try {
                        $days = Carbon::parse($lb['date_start'])->diffInDays(Carbon::parse($lb['date_end'])) + 1;
                        if ($days < 1) $days = 1;
                    } catch (\Exception $e) { $days = 1; }
                }
                $ldata = [
                    'purchase_id' => $purchase->id,
                    'labour' => $lb['labour'] ?? null,
                    'numbers' => $numbers,
                    'date_start' => $lb['date_start'] ?? null,
                    'date_end' => $lb['date_end'] ?? null,
                    'amount' => $amount,
                    'total_amount' => round($numbers * $amount * $days, 2),
                    'note' => $lb['note'] ?? null,
                ];
                PurchaseLabour::create($ldata);
            }
        }

        return redirect()->route('purchase.index')->with('success', 'Purchase updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('purchase-delete')) {
            abort(403);
        }

        $purchase = Purchase::findOrFail($id);
        if (strtolower($purchase->status) === 'paid') {
            return redirect()->route('purchase.index')->with('error', 'Paid purchases cannot be deleted');
        }
        $purchase->delete();
        return redirect()->route('purchase.index')->with('success', 'Purchase deleted');
    }

    public function show($id): View
    {
        $purchase = Purchase::with(['vendor', 'project', 'purchaseItems.item', 'purchaseItems.subCategory'])->findOrFail($id);
        return view('admin.purchase.show', compact('purchase'));
    }

    public function list(Request $request)
    {
        try {
            $columns = [0 => 'id', 1 => 'vendor', 2 => 'project', 3 => 'sub_category', 4 => 'amount', 5 => 'due_amount', 6 => 'status', 7 => 'purchase_date', 8 => 'action'];

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
                      ->orWhere('due_amount', 'like', "%{$search}%")
                      ->orWhere('status', 'like', "%{$search}%");
                });
            }

            // filter by vendor or status if provided (used by payments UI)
            if ($request->filled('vendor_id')) {
                $query->where('vendor_id', $request->input('vendor_id'));
            }
            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }

            // optional date range filter
            if ($request->filled('date_from')) {
                $query->whereDate('purchase_date', '>=', $request->input('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->whereDate('purchase_date', '<=', $request->input('date_to'));
            }

            $totalData = Purchase::count();
            $totalFiltered = $query->count();

            $allowedOrders = ['id', 'amount', 'due_amount', 'purchase_date', 'status'];
            $orderBy = in_array($order, $allowedOrders) ? $order : 'id';

            $rows = $query->with(['purchaseItems','purchaseLabours'])->offset($start)
                ->limit($limit)
                ->orderBy($orderBy, $dir)
                ->get();

            $data = [];
            $i = $start + 1;
            foreach ($rows as $row) {
                $nested = [];
                $nested['id'] = $row->id;
                $nested['purchase_id'] = $row->id;
                $nested['vendor'] = $row->vendor->name ?? '';
                $nested['project'] = $row->project->name ?? '';
                $nested['project_id'] = $row->project_id;
                $nested['sub_category'] = $row->subCategory->name ?? '';
                $nested['amount'] = $row->amount;
                $nested['due_amount'] = $row->due_amount;
                $nested['status'] = ucfirst($row->status ?? '');
                $nested['purchase_date'] = $row->purchase_date;

                $actions = '<div class="btn-group">';
                $actions .= "<i class=\"fas fa-ellipsis-v\" data-toggle=\"dropdown\" style=\"cursor:pointer;\"></i>";
                $actions .= '<div class="dropdown-menu dropdown-menu-right" style="min-width: 50px; padding: 0;">';
                $auth = auth()->user();
                $canEdit = $auth?->hasPermission('purchase-edit') ?? false;
                $canDelete = $auth?->hasPermission('purchase-delete') ?? false;

                $actions .= '<a href="' . route('purchase.show', $row->id) . '" class="table-action-btn is-view" title="View"><i class="fa fa-eye"></i></a>';
                if ($canEdit) {
                    $actions .= '<a href="' . route('purchase.edit', $row->id) . '" class="table-action-btn is-edit" title="Edit"><i class="fa fa-edit"></i></a>';
                }
                if ($canDelete) {
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
