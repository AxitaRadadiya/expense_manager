<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubCategory;
use App\Models\Category;

class SubCategoryController extends Controller
{
    public function index()
    {
        // only show the two seeded categories (Income, Expense)
        $categories = Category::whereIn('name', ['Income', 'Expense'])->pluck('name', 'id');
        return view('admin.subcategory.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
        ]);

        SubCategory::create($request->only(['category_id','name']));

        return redirect()->back()->with('success', 'Sub category added successfully');
    }

    public function destroy(SubCategory $subcategory)
    {
        $subcategory->delete();
        return redirect()->back()->with('success', 'Sub category deleted successfully');
    }

    public function list(Request $request)
    {
        try {
            $columns = [0 => 'id', 1 => 'name', 2 => 'category_id', 3 => 'action'];

            $limit = intval($request->input('length', 10));
            $start = intval($request->input('start', 0));
            $orderColumnIndex = intval($request->input('order.0.column', 1));
            $order = $columns[$orderColumnIndex] ?? 'name';
            $dir = $request->input('order.0.dir', 'asc');
            $search = $request->input('search.value');

            $query = SubCategory::with('category');
            if (!empty($search)) {
                $query->where('name', 'like', "%{$search}%");
            }

            $totalData = SubCategory::count();
            $totalFiltered = $query->count();

            $rows = $query->offset($start)->limit($limit)->orderBy($order, $dir)->get();

            $data = [];
            $i = $start + 1;
            foreach ($rows as $row) {
                $nested = [];
                $nested['id'] = $i;
                $nested['name'] = $row->name;
                $nested['category'] = $row->category->name ?? '-';

                $actions = '<div class="btn-group">';
                $actions .= "<i class=\"fas fa-ellipsis-v\" data-toggle=\"dropdown\" style=\"cursor:pointer;\"></i>";
                $actions .= '<div class="dropdown-menu dropdown-menu-right" style="min-width: 50px; padding: 0;">';
                if (auth()->check()) {
                    $actions .= '<a href="#" data-id="' . $row->id . '" data-name="' . htmlspecialchars($row->name, ENT_QUOTES) . '" data-category="' . ($row->category->id ?? '') . '" class="table-action-btn is-edit edit-subcategory-modal" title="Edit"><i class="fa fa-edit"></i></a>';
                    $actions .= '<form action="' . route('sub-category.destroy', $row->id) . '" method="POST" class="table-action-form">' . csrf_field() . '<input type="hidden" name="_method" value="DELETE">' . '<button type="submit" class="table-action-btn is-delete deleteButton" title="Delete"><i class="fa fa-trash"></i></button></form>';
                }
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

        } catch (\Exception $e) {
            \Log::error('SubCategory list error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
        ]);

        $subcategory = SubCategory::findOrFail($id);
        $subcategory->update($request->only(['category_id', 'name']));

        return redirect()->back()->with('success', 'Sub category updated successfully');
    }
}
