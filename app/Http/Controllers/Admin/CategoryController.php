<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
   
    public function index()
    {
        $categories = Category::latest()->get();
        return view('admin.category.index', compact('categories'));
    }

    public function create()
    {
       
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Category::create([
            'name' => $request->name
        ]);

        return redirect()->back()->with('success', 'Category added successfully');
    }


    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update([
            'name' => $request->name
        ]);

        return redirect()->back()->with('success', 'Category updated successfully');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->back()->with('success', 'Category deleted successfully');
    }
        public function list(Request $request) 
        {
            try {
                $columns = array(
                    0 => 'position',
                    1 => 'name',
                    2 => 'action'
                );

                $limit = intval($request->input('length', 10));
                $start = intval($request->input('start', 0));
                $orderColumnIndex = $request->input('order.0.column', 1);
                $order = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : 'name';
                $dir = $request->input('order.0.dir', 'asc');
                $search = $request->input('search.value');

                $query = Category::query();
                if (!empty($search)) {
                    $query->where('name', 'like', "%$search%");
                }

                $totalData = Category::count();
                $totalFiltered = $query->count();

                $rows = $query->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();

                $data = [];
                $i = $start + 1;
                foreach ($rows as $row) {
                    $nestedData = [];
                    $nestedData['position'] = $row->position ?? null;
                    $nestedData['id'] = $i;
                    $nestedData['name'] = $row->name;

                    $actions = '<div class="btn-group">';
                    if (auth()->user()) {
                        $actions .= '<a href="#" data-id="' . $row->id . '" data-name="' . htmlspecialchars($row->name, ENT_QUOTES) . '" class="btn-sm edit-category-date-modal"><i class="fa fa-edit"></i></a>';
                        $actions .= '<form action="' . route('category.destroy', $row->id) . '" method="POST" class="deleteForm">' . csrf_field() . '<input type="hidden" name="_method" value="DELETE"><button type="submit" class="deleteButton border-0 bg-white text-danger"><i class="fa fa-trash"></i></button></form>';
                    }
                    $actions .= '</div>';

                    $nestedData['action'] = $actions;
                    $data[] = $nestedData;
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
                \Log::error('Category list error: ' . $e->getMessage());
                return response()->json(['error' => 'Server error', 'message' => $e->getMessage()], 500);
            }
        }
}
