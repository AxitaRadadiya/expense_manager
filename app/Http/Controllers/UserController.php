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
use App\Models\UserBalanceHistory;
use Illuminate\Support\Facades\DB;

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
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'unique:users,email'],
            'mobile'      => ['nullable', 'string', 'max:15'],
            'role_id'     => ['required', 'exists:roles,id'],
            'status'      => ['required', 'in:0,1'],
            'note'        => ['nullable', 'string', 'max:1000'],
            'password'    => ['required', 'string', 'min:8', 'confirmed'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'amount'      => ['nullable', 'numeric', 'min:0'],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'mobile'   => $request->mobile,
            'role_id'  => $request->role_id,
            'status'   => $request->status,
            'note'     => $request->note,
            'amount'     => $request->amount,
            'project_id' => $request->project_id,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole((int) $request->role_id);

        // Persist single project via `project_id` column (no pivot sync required)
        // `project_id` was already stored in the create above.

        // If an opening amount was provided, record opening balance history
        if (! empty($request->amount) && (float)$request->amount != 0) {
            UserBalanceHistory::create([
                'user_id' => $user->id,
                'change_type' => 'opening',
                'change_amount' => $request->amount,
                'balance_before' => 0,
                'balance_after' => $request->amount,
                'reference_type' => 'user',
                'reference_id' => $user->id,
                'created_by' => Auth::id(),
                'note' => 'Opening balance set',
            ]);
        }

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

        // Expenses (paginated) and totals
        $expensesQuery = Expense::with('project')->where('users_id', $user->id);
        $totalDebited = (float) $expensesQuery->sum('amount');
        $expenses = $expensesQuery->latest()->paginate(15);

        // Transfers (paginated) and totals
        $transfersQuery = \App\Models\Transfer::where('user_id', $user->id);
        $totalTransfers = (float) $transfersQuery->sum('amount');
        $transfers = $transfersQuery->latest()->paginate(15, ['*'], 'transfers_page');

        // Balance histories (paginated)
        $balanceHistories = \App\Models\UserBalanceHistory::where('user_id', $user->id)->latest()->paginate(15, ['*'], 'balances_page');

        // Opening balance from user.amount
        $opening = (float) $user->amount;

        // Current balance calculation: opening + transfers - debited
        $currentBalance = $opening + $totalTransfers - $totalDebited;

        return view('admin.users.show', [
            'user' => $user,
            'expenses' => $expenses,
            'totalDebited' => $totalDebited,
            'transfers' => $transfers,
            'totalTransfers' => $totalTransfers,
            'balanceHistories' => $balanceHistories,
            'opening' => $opening,
            'currentBalance' => $currentBalance,
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
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'unique:users,email,' . $user->id],
            'mobile'      => ['nullable', 'string', 'max:15'],
            'role_id'     => ['required', 'exists:roles,id'],
            'status'      => ['required', 'in:0,1'],
            'note'        => ['nullable', 'string', 'max:1000'],
            'password'    => ['nullable', 'string', 'min:8', 'confirmed'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'amount'      => ['nullable', 'numeric', 'min:0'],
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
        // capture original project_id for comparison
        $originalProject = $user->project_id;

        $user->update($data);
        $user->assignRole((int) $request->role_id);

        $changes = [];
        foreach ($data as $field => $newVal) {
            if ($field === 'password') continue;
            if (isset($original[$field]) && (string) $original[$field] !== (string) $newVal) {
                $changes[$field] = ['old' => $original[$field], 'new' => $newVal];
            }
        }

        // project change detection
        if ((string)$originalProject !== (string)$user->project_id) {
            $changes['project'] = ['old' => $originalProject, 'new' => $user->project_id];
        }

        // amount change: store balance history
        $oldAmount = isset($original['amount']) ? (float)$original['amount'] : 0.0;
        $newAmount = (float)$user->amount;
        if ($oldAmount !== $newAmount) {
            $diff = $newAmount - $oldAmount;
            UserBalanceHistory::create([
                'user_id' => $user->id,
                'change_type' => 'adjustment',
                'change_amount' => $diff,
                'balance_before' => $oldAmount,
                'balance_after' => $newAmount,
                'reference_type' => 'user',
                'reference_id' => $user->id,
                'created_by' => Auth::id(),
                'note' => 'Admin updated balance',
            ]);
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
            $query = User::with(['role', 'project']);

            $totalData = $query->count();
            $totalFiltered = $totalData;

            $limit = $request->input('length');
            $start = $request->input('start');
            $search = $request->input('search.value');

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
                });

                $totalFiltered = $query->count();
            }

            $users = $query->offset($start)
                ->limit($limit)
                ->orderBy('id', 'desc')
                ->get();

            $data = [];

            foreach ($users as $i => $u) {

                $viewUrl = route('users.show', $u->id);
                $editUrl = route('users.edit', $u->id);
                $deleteUrl = route('users.destroy', $u->id);

                $actionHtml  = '<a href="' . $viewUrl . '" class="btn btn-sm btn-info" title="View"><i class="fas fa-eye"></i></a> ';
                $actionHtml .= '<a href="' . $editUrl . '" class="btn btn-sm btn-primary" title="Edit"><i class="fas fa-edit"></i></a> ';
                $actionHtml .= '<form method="POST" action="' . $deleteUrl . '" style="display:inline-block;margin:0;padding:0;">';
                $actionHtml .= '<input type="hidden" name="_token" value="' . csrf_token() . '">';
                $actionHtml .= '<input type="hidden" name="_method" value="DELETE">';
                $actionHtml .= '<button type="submit" class="btn btn-sm btn-danger deleteButton" title="Delete"><i class="fas fa-trash"></i></button>';
                $actionHtml .= '</form>';

                $data[] = [
                    'id'      => $start + $i + 1,
                    'name'    => $u->name,
                    'email'   => $u->email,
                    'mobile'  => $u->mobile,
                    'project' => optional($u->project)->name,
                    'amount'  => $u->amount,
                    'role'    => optional($u->role)->name,
                    'status'  => $u->status ? 'Active' : 'Inactive',
                    'action'  => $actionHtml
                ];
            }

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalData,
                'recordsFiltered' => $totalFiltered,
                'data' => $data,
            ]);
        }
}