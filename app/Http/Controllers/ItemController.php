<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    
    public function index()
    {
        $items = Item::latest()->get();
        return view('admin.items.index', compact('items'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Item::create([
            'name' => $request->name
        ]);

        return redirect()->back()->with('success', 'Item created successfully.');
    }

    public function show(Item $item)
    {
        //
    }

    public function edit(Item $item)
    {
        //
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $item->update([
            'name' => $request->name
        ]);

        return redirect()->back()->with('success', 'Item updated successfully.');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->back()->with('success', 'Item deleted successfully.');
    }

    public function list(Request $request)
    {
        try {
            $columns = [
                0 => 'id',
                1 => 'name',
                2 => 'created_at',
                3 => 'action',
            ];

            $limit = intval($request->input('length', 10));
            $start = intval($request->input('start', 0));
            $orderColumnIndex = intval($request->input('order.0.column', 1));
            $order = $columns[$orderColumnIndex] ?? 'name';
            $dir = $request->input('order.0.dir', 'asc');
            $search = $request->input('search.value');

            $query = Item::query();
            if (!empty($search)) {
                $query->where('name', 'like', "%{$search}%");
            }

            $totalData = Item::count();
            $totalFiltered = $query->count();

            $rows = $query->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $data = [];
            $i = $start + 1;
            foreach ($rows as $row) {
                $nested = [];
                $nested['id'] = $i;
                $nested['name'] = $row->name;

                $actions = '<div class="btn-group">';
                $actions .= "<i class=\"fas fa-ellipsis-v\" data-toggle=\"dropdown\" style=\"cursor:pointer;\"></i>";
                $actions .= '<div class="dropdown-menu dropdown-menu-right" style="min-width: 50px; padding: 0;">';

                if (auth()->check()) {
                    $actions .= '<a href="#" data-id="' . $row->id . '" data-name="' . htmlspecialchars($row->name, ENT_QUOTES) . '" class="table-action-btn is-edit edit-item-modal" title="Edit"><i class="fa fa-edit"></i></a>';
                    $actions .= '<form action="' . route('item.destroy', $row->id) . '" method="POST" class="table-action-form">' . csrf_field() . '<input type="hidden" name="_method" value="DELETE">' . '<button type="button" class="table-action-btn is-delete deleteButton" title="Delete"><i class="fa fa-trash"></i></button></form>';
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
            \Log::error('Item list error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error', 'message' => $e->getMessage()], 500);
        }
    }
}
