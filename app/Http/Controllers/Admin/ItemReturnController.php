<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ItemReturn;
use App\Models\Item;
use App\Models\Project;

class ItemReturnController extends Controller
{
    public function index()
    {
        return view('admin.item_return.index');
    }

    public function create()
    {
        $items = Item::pluck('name', 'id');
        $projects = Project::pluck('name', 'id');
        return view('admin.item_return.create', compact('items', 'projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'item_id' => 'required|exists:items,id',
            'date' => 'required|date',
            'total_number' => 'nullable|integer',
        ]);

        ItemReturn::create($request->only(['project_id','item_id','date','total_number']));

        return redirect()->route('item-return.index')->with('success', 'Item return created successfully.');
    }

    public function show(ItemReturn $itemReturn)
    {
        return view('admin.item_return.show', compact('itemReturn'));
    }

    public function edit(ItemReturn $itemReturn)
    {
        $items = Item::pluck('name', 'id');
        $projects = Project::pluck('name', 'id');
        return view('admin.item_return.edit', compact('itemReturn', 'items', 'projects'));
    }

    public function update(Request $request, ItemReturn $itemReturn)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'item_id' => 'required|exists:items,id',
            'date' => 'required|date',
            'total_number' => 'nullable|integer',
        ]);

        $itemReturn->update($request->only(['project_id','item_id','date','total_number']));

        return redirect()->route('item-return.index')->with('success', 'Item return updated successfully.');
    }

    public function destroy(ItemReturn $itemReturn)
    {
        $itemReturn->delete();
        return redirect()->route('item-return.index')->with('success', 'Item return deleted successfully.');
    }

    // Server-side DataTable list
    public function list(Request $request)
    {
        // Map DataTable column index to actual DB column names
        $columns = [
            0 => 'id',
            1 => 'project_id',
            2 => 'item_id',
            3 => 'date',
            4 => 'total_number',
            5 => 'id'
        ];

        $limit = intval($request->input('length', 10));
        $start = intval($request->input('start', 0));
        $orderColumnIndex = intval($request->input('order.0.column', 1));
        $order = $columns[$orderColumnIndex] ?? 'id';
        $dir = $request->input('order.0.dir', 'asc');
        $search = $request->input('search.value');

        $query = ItemReturn::with(['item','project']);
        if (!empty($search)) {
            $query->whereHas('item', function($q) use ($search) { $q->where('name', 'like', "%{$search}%"); })
                ->orWhereHas('project', function($q) use ($search) { $q->where('name', 'like', "%{$search}%"); });
        }

        $totalData = ItemReturn::count();
        $totalFiltered = $query->count();

        $rows = $query->offset($start)->limit($limit)->orderBy($order, $dir)->get();

        $data = [];
        $i = $start + 1;
        foreach ($rows as $row) {
            $nested = [];
            $nested['id'] = $i;
            $nested['project'] = $row->project->name ?? '-';
            $nested['item'] = $row->item->name ?? '-';
            $nested['date'] = $row->date ? $row->date->format('Y-m-d') : '-';
            $nested['total_number'] = $row->total_number ?? '-';

            $actions = '<div class="btn-group">';
            $actions .= "<i class=\"fas fa-ellipsis-v\" data-toggle=\"dropdown\" style=\"cursor:pointer;\"></i>";
            $actions .= '<div class="dropdown-menu dropdown-menu-right" style="min-width: 50px; padding: 0;">';
            $actions .= '<a href="' . route('item-return.show', $row->id) . '" class="table-action-btn is-view" title="View"><i class="fa fa-eye"></i></a>';
            $actions .= '<a href="' . route('item-return.edit', $row->id) . '" class="table-action-btn is-edit" title="Edit"><i class="fa fa-edit"></i></a>';
            $actions .= '<form action="' . route('item-return.destroy', $row->id) . '" method="POST" class="table-action-form">' . csrf_field() . '<input type="hidden" name="_method" value="DELETE">' . '<button type="button" class="table-action-btn is-delete deleteButton" title="Delete"><i class="fa fa-trash"></i></button></form>';
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
