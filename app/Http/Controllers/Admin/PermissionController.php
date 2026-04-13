<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        return view('admin.permissions.index', [
            'permissions' => Permission::orderByDesc('id')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.permissions.create', [
            'permissions' => Permission::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
        ]);

        Permission::create($validated);

        return redirect()->route('permissions.index')
            ->withSuccess('New Permission is added successfully.');
    }

    public function edit(string $id): View
    {
        $permission = Permission::findOrFail($id);

        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $permission = Permission::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name,' . $permission->id],
        ]);

        $permission->update($validated);

        return redirect()->route('permissions.index')
            ->withSuccess('Permission is updated successfully.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return redirect()->route('permissions.index')
            ->withSuccess('Permission is deleted successfully.');
    }

    public function permissionsList(Request $request)
    {
        $columns = [
            0 => 'id',
            1 => 'name',
            2 => 'id',
        ];

        $limit = (int) $request->input('length', 10);
        $start = (int) $request->input('start', 0);
        $order = $columns[$request->input('order.0.column')] ?? 'id';
        $dir = $request->input('order.0.dir') === 'asc' ? 'asc' : 'desc';
        $search = trim((string) $request->input('search.value', ''));

        $query = Permission::query();

        if ($search !== '') {
            $query->where('name', 'like', "%{$search}%");
        }

        $totalData = Permission::count();
        $totalFiltered = $query->count();

        $permissions = (clone $query)
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        $canEditPermission = auth()->user()?->can('permission-edit') ?? false;
        $canDeletePermission = auth()->user()?->can('permission-delete') ?? false;

        $data = $permissions->map(function (Permission $permission, int $index) use ($start, $canEditPermission, $canDeletePermission) {
            $actions = [];

            if ($canEditPermission) {
                $actions[] = '<a href="' . route('permissions.edit', $permission->id) . '" class="btn btn-success btn-sm"><i class="fa fa-edit"></i></a>';
            }

            if ($canDeletePermission) {
                $actions[] = '<form action="' . route('permissions.destroy', $permission->id) . '" method="POST" class="deleteForm d-inline">'
                    . csrf_field()
                    . '<input type="hidden" name="_method" value="DELETE">'
                    . '<button type="submit" class="btn btn-danger btn-sm deleteButton"><i class="fa fa-trash"></i></button>'
                    . '</form>';
            }

            return [
                'id' => $start + $index + 1,
                'name' => $permission->name,
                'action' => implode('&nbsp;', $actions),
            ];
        })->all();

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }
}
