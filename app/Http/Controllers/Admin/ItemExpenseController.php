<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ItemExpense;
use App\Models\Item;
use App\Models\Project;
use App\Models\User;
use App\Models\Role;

class ItemExpenseController extends Controller
{
    public function index()
    {
        return view('admin.item_expense.index');
    }

    public function create()
    {
        $items = Item::pluck('name', 'id');
        $projects = Project::pluck('name', 'id');
        $roleId = Role::where('name', 'vendor')->value('id');
        $vendors = User::where('role_id', $roleId)->pluck('name', 'id');
        $users = User::where('role_id', '!=', 5)->pluck('name', 'id');

        return view('admin.item_expense.create', compact('items', 'projects', 'vendors', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'vendor_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'total_number' => 'nullable|integer',
            'total_amount' => 'required|numeric',
        ]);

        ItemExpense::create($request->only(['item_id','vendor_id','project_id','user_id','start_date','end_date','total_number','total_amount']));

        return redirect()->route('item-expense.index')->with('success', 'Item expense created successfully.');
    }

    public function show(ItemExpense $itemExpense)
    {
        return view('admin.item_expense.show', compact('itemExpense'));
    }

    public function edit(ItemExpense $itemExpense)
    {
        $items = Item::pluck('name', 'id');
        $projects = Project::pluck('name', 'id');
        $roleId = Role::where('name', 'vendor')->value('id');
        $vendors = User::where('role_id', $roleId)->pluck('name', 'id');
        $users = User::where('role_id', '!=', 5)->pluck('name', 'id');

        return view('admin.item_expense.edit', compact('itemExpense', 'items', 'projects', 'vendors', 'users'));
    }

    public function update(Request $request, ItemExpense $itemExpense)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'vendor_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'total_number' => 'nullable|integer',
            'total_amount' => 'required|numeric',
        ]);

        $itemExpense->update($request->only(['item_id','vendor_id','project_id','user_id','start_date','end_date','total_number','total_amount']));

        return redirect()->route('item-expense.index')->with('success', 'Item expense updated successfully.');
    }

    public function destroy(ItemExpense $itemExpense)
    {
        $itemExpense->delete();
        return redirect()->route('item-expense.index')->with('success', 'Item expense deleted successfully.');
    }

    // Server-side DataTable list
    public function list(Request $request)
    {
        // Map DataTable column index to actual DB column names to prevent SQL errors
        $columns = [
            0 => 'id',
            1 => 'item_id',
            2 => 'vendor_id',
            3 => 'project_id',
            4 => 'start_date',
            5 => 'end_date',
            6 => 'user_id',
            7 => 'total_number',
            8 => 'total_amount',
            9 => 'id'
        ];

        $limit = intval($request->input('length', 10));
        $start = intval($request->input('start', 0));
        $orderColumnIndex = intval($request->input('order.0.column', 1));
        $order = $columns[$orderColumnIndex] ?? 'id';
        $dir = $request->input('order.0.dir', 'asc');
        $search = $request->input('search.value');

        $query = ItemExpense::with(['item','vendor','project','user']);
        if (!empty($search)) {
            $query->whereHas('item', function($q) use ($search) { $q->where('name', 'like', "%{$search}%"); })
                ->orWhereHas('vendor', function($q) use ($search) { $q->where('name', 'like', "%{$search}%"); })
                ->orWhereHas('project', function($q) use ($search) { $q->where('name', 'like', "%{$search}%"); });
        }

        $totalData = ItemExpense::count();
        $totalFiltered = $query->count();

        $rows = $query->offset($start)->limit($limit)->orderBy($order, $dir)->get();

        $data = [];
        $i = $start + 1;
        foreach ($rows as $row) {
            $nested = [];
            $nested['id'] = $i;
            $nested['item'] = $row->item->name ?? '-';
            $nested['vendor'] = $row->vendor->name ?? '-';
            $nested['project'] = $row->project->name ?? '-';
            $nested['start_date'] = $row->start_date ? $row->start_date->format('Y-m-d') : '-';
            $nested['end_date'] = $row->end_date ? $row->end_date->format('Y-m-d') : '-';
            $nested['user'] = $row->user->name ?? '-';
            $nested['total_number'] = $row->total_number ?? '-';
            $nested['total_amount'] = number_format($row->total_amount, 2);

            $actions = '<div class="btn-group">';
            $actions .= "<i class=\"fas fa-ellipsis-v\" data-toggle=\"dropdown\" style=\"cursor:pointer;\"></i>";
            $actions .= '<div class="dropdown-menu dropdown-menu-right" style="min-width: 50px; padding: 0;">';
            $actions .= '<a href="' . route('item-expense.show', $row->id) . '" class="table-action-btn is-view" title="View"><i class="fa fa-eye"></i></a>';
            $actions .= '<a href="' . route('item-expense.edit', $row->id) . '" class="table-action-btn is-edit" title="Edit"><i class="fa fa-edit"></i></a>';
            $actions .= '<form action="' . route('item-expense.destroy', $row->id) . '" method="POST" class="table-action-form">' . csrf_field() . '<input type="hidden" name="_method" value="DELETE">' . '<button type="button" class="table-action-btn is-delete deleteButton" title="Delete"><i class="fa fa-trash"></i></button></form>';
            $actions .= '</div></div>';

            $nested['action'] = $actions;
            $data[] = $nested;
            $i++;
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => intval($totalFiltered),
            'data' => $data,
        ]);
    }
}
