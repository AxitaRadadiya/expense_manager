<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Role;
use App\Models\Project;
use Illuminate\View\View;
use App\Models\User;
use App\Models\Expense;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /* ─────────────────────────────────────────
     | INDEX
     ───────────────────────────────────────── */
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::with('role')->orderBy('id')->paginate(15),
        ]);
    }

    /* ─────────────────────────────────────────
     | CREATE
     ───────────────────────────────────────── */
    public function create(): View
    {
        return view('admin.users.create', [
            'roles' => Role::orderBy('name')->get(),
            'projects' => Project::orderBy('name')->get(),
        ]);
    }

    /* ─────────────────────────────────────────
     | STORE
     ───────────────────────────────────────── */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'mobile'   => ['nullable', 'string', 'max:15'],
            'role_id'  => ['required', 'exists:roles,id'],
            'status'   => ['required', 'in:0,1'],
            'note'     => ['nullable', 'string', 'max:1000'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'amount'     => ['nullable', 'numeric', 'min:0'],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'mobile'   => $request->mobile,
            'role_id'  => $request->role_id,
            'status'   => $request->status,
            'note'     => $request->note,
            'project_id' => $request->project_id,
            'amount'     => $request->amount,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole((int) $request->role_id);

        $loginUser = Auth::user();
        Log::info('user.created', [
            'actor_id' => $loginUser->id,
            'actor_name' => $loginUser->name,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'properties' => $request->except(['_token', 'password', 'password_confirmation']),
        ]);

        return redirect()->route('users.index')
            ->withSuccess('New user added successfully.');
    }

    public function show(User $user): View
    {
        $user->load('role');

        $expenses = Expense::with('project')
            ->where('users_id', $user->id)
            ->latest()
            ->paginate(15);

        return view('admin.users.show', [
            'user' => $user,
            'expenses' => $expenses,
        ]);
    }

    public function edit($id): View
    {
        $user = User::with('role')->findOrFail($id);

        return view('admin.users.edit', [
            'user'  => $user,
            'roles' => Role::orderBy('name')->get(),
            'projects' => Project::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email,' . $user->id],
            'mobile'   => ['nullable', 'string', 'max:15'],
            'role_id'  => ['required', 'exists:roles,id'],
            'status'   => ['required', 'in:0,1'],
            'note'     => ['nullable', 'string', 'max:1000'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'amount'     => ['nullable', 'numeric', 'min:0'],
        ]);

        $data = [
            'name'    => $request->name,
            'email'   => $request->email,
            'mobile'  => $request->mobile,
            'role_id' => $request->role_id,
            'status'  => $request->status,
            'note'    => $request->note,
            'project_id' => $request->project_id,
            'amount'     => $request->amount,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $original = $user->getOriginal();
        $user->update($data);
        $user->assignRole((int) $request->role_id);

        // Detect changed fields for activity log
        $changes = [];
        foreach ($data as $field => $newVal) {
            if ($field === 'password') continue;
            if (isset($original[$field]) && (string) $original[$field] !== (string) $newVal) {
                $changes[$field] = ['old' => $original[$field], 'new' => $newVal];
            }
        }

        $loginUser = Auth::user();
        if (!empty($changes)) {
            Log::info('user.updated', [
                'actor_id' => $loginUser->id,
                'actor_name' => $loginUser->name,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'changes' => $changes,
            ]);
        }

        return redirect()->route('users.index')
            ->withSuccess('User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $loginUser = Auth::user();

        Log::info('user.deleted', [
            'actor_id' => $loginUser->id,
            'actor_name' => $loginUser->name,
            'user_id' => $user->id,
            'user_name' => $user->name,
        ]);

        $user->syncRoles([]);
        $user->delete();

        return redirect()->route('users.index')
            ->withSuccess('User deleted successfully.');
    }

    public function userList(Request $request)
    {
        $columns = [0 => 'id', 1 => 'name', 2 => 'email', 3 => 'mobile', 4 => 'project', 5 => 'amount', 6 => 'status', 7 => 'action'];

        $totalData     = User::count();
        $totalFiltered = $totalData;
        $limit         = $request->input('length');
        $start         = $request->input('start');
        $order         = $columns[$request->input('order.0.column')] ?? 'id';
        $dir           = $request->input('order.0.dir', 'desc');
        $search        = $request->input('search.value');

        $query = User::with('role');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name',   'like', "%{$search}%")
                  ->orWhere('email',  'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
            $totalFiltered = $query->count();
        }

        $users = $query->offset($start)->limit($limit)->orderBy($order, $dir)->get();

        $data = [];
        foreach ($users as $i => $u) {
            // Inline action icons (no three-dots dropdown)
            $actions = '<div class="action-icons" style="display:flex;gap:8px;align-items:center;">';

            if (auth()->user()) {
                $actions .= '<a href="' . route('users.show', $u->id) . '" title="View" class="text-decoration-none" style="color:#C9960C;"><i class="fas fa-eye"></i></a>';
            }
            if (auth()->user()) {
                $actions .= '<a href="' . route('users.edit', $u->id) . '" title="Edit" class="text-decoration-none" style="color:#2563eb;"><i class="fas fa-pen"></i></a>';
            }
            if (auth()->user()) {
                $actions .= '<form action="' . route('users.destroy', $u->id) . '" method="POST" class="d-inline-block" onsubmit="return confirm(\'Are you sure you want to delete this user?\');">'
                    . csrf_field()
                    . '<input type="hidden" name="_method" value="DELETE">'
                    . '<button type="submit" class="btn btn-link p-0 m-0" style="color:#dc2626;" title="Delete"><i class="fas fa-trash"></i></button>'
                    . '</form>';
            }

            $actions .= '</div>';

            // Status badge
            $statusBadge = $u->status
                ? '<span class="sb sb-completed">Active</span>'
                : '<span class="sb sb-cancelled">Inactive</span>';

            $data[] = [
                'id'     => $start + $i + 1,
                'name'   => '<span style="font-weight:600;color:#0D1A30;">' . e($u->name) . '</span>',
                'email'  => '<span style="color:#64748b;">' . e($u->email) . '</span>',
                'mobile'  => '<span style="color:#64748b;">' . e($u->mobile ?? '—') . '</span>',
                'project' => '<span style="color:#64748b;">' . e($u->project ?? '—') . '</span>',
                'amount'  => $u->amount !== null
                    ? '<span style="color:#0D1A30;font-weight:600;">' . $u->amount . '</span>'
                    : '<span style="color:#ccc;">—</span>',
                'role'    => $u->role
                    ? '<span class="role-chip">' . e($u->role->name) . '</span>'
                    : '<span style="color:#ccc;">—</span>',
                'status' => $statusBadge,
                'action' => $actions,
            ];
        }

        echo json_encode([
            'draw'            => intval($request->input('draw')),
            'recordsTotal'    => intval($totalData),
            'recordsFiltered' => intval($totalFiltered),
            'data'            => $data,
        ]);
    }
}