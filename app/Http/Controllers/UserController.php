<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Role;
use Illuminate\View\View;
use App\Models\User;
use App\Models\Transfer;
use App\Models\Expense;
use App\Models\UserBalanceHistory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\BalanceService;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function __construct(protected BalanceService $balanceService)
    {
        $this->middleware('auth');
    }

    /* ─────────────────────────────────────────
     | INDEX
     ───────────────────────────────────────── */
    public function index(): View
    {
        $excludedRoleIds = Role::whereIn('name', ['vendor', 'customer'])
            ->pluck('id');
        return view('admin.users.index', [
            'users' => User::with(['role', 'projects'])->whereNotIn('role_id', $excludedRoleIds)->orderBy('id')->paginate(15),
        ]);
    }

    /* ─────────────────────────────────────────
     | CREATE
     ───────────────────────────────────────── */
    public function create(): View
    {
        $excludedRoleIds = Role::whereIn('name', ['vendor', 'customer'])
    ->pluck('id');
        return view('admin.users.create', [
            'roles' => Role::whereNotIn('id', $excludedRoleIds)
                            ->orderBy('name')
                            ->get(),
        ]);
    }

    /* ─────────────────────────────────────────
     | STORE
     ───────────────────────────────────────── */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255', 'regex:/^[A-Za-z ]+$/'],
            'email'       => ['required', 'email', 'unique:users,email'],
            'mobile'      => ['nullable', 'regex:/^\d{10}$/'],
            'role_id'     => ['required', 'exists:roles,id'],
            'status'      => ['required', 'in:0,1'],
            'note'        => ['nullable', 'string', 'max:1000'],
            'password'    => ['required', Password::min(8)->mixedCase()->numbers()->symbols(), 'confirmed'],
            'amount'      => ['nullable', 'numeric', 'min:0'],
        ]);

        $loginUser = Auth::user();
        $openingAmount = round((float) ($request->amount ?? 0), 2);
        $hasInsufficientBalance = false;

        $user = DB::transaction(function () use ($request, $loginUser, $openingAmount, &$hasInsufficientBalance) {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'mobile'   => $request->mobile,
                'role_id'  => $request->role_id,
                'status'   => $request->status,
                'note'     => $request->note,
                'amount'   => $openingAmount,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole((int) $request->role_id);

            $this->balanceService->recordOpeningBalance(
                $user,
                $openingAmount,
                Auth::id(),
                $openingAmount > 0 ? 'Opening amount funded during user creation' : 'Opening balance set'
            );

            if ($loginUser && $openingAmount > 0) {
                $loginUser->refresh();

                $creatorOldAmount = round((float) ($loginUser->amount ?? 0), 2);
                $creatorNewAmount = round($creatorOldAmount - $openingAmount, 2);

                $hasInsufficientBalance = $openingAmount > $creatorOldAmount;

                $loginUser->update([
                    'amount' => $creatorNewAmount,
                ]);

                $this->balanceService->recordAdjustment(
                    $loginUser,
                    $creatorOldAmount,
                    $creatorNewAmount,
                    Auth::id(),
                    'Opening amount allocated to user: ' . $user->name
                );
            }

            return $user;
        });

        Log::info('user.created', [
            'actor_id' => $loginUser->id,
            'actor_name' => $loginUser->name,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'properties' => $request->except(['_token', 'password', 'password_confirmation']),
        ]);

        if ($hasInsufficientBalance) {
            return redirect()->route('users.index')
                ->with('warning', 'User created successfully, but your balance became negative after funding the opening amount.');
        }

        return redirect()->route('users.index')
            ->withSuccess('New user added successfully.');
    }

    public function show(User $user): View
    {
        $user->load(['role', 'projects']);

        // Expenses (paginated) and totals
        $expensesQuery = Expense::with('project')->where('users_id', $user->id);
        $totalDebited = (float) $expensesQuery->sum('amount');
        $expenses = $expensesQuery->latest()->paginate(5);

        // Transfers (paginated) and totals
        $transfersQuery = Transfer::where('user_id', $user->id);
        $totalTransfers = (float) $transfersQuery->sum('amount');
        $totalTransfersSent = (float) Transfer::where('created_by', $user->id)->sum('amount');
        $transfers = $transfersQuery->latest()->paginate(5, ['*'], 'transfers_page');

        // Balance histories (paginated)
        $balanceHistories = UserBalanceHistory::where('user_id', $user->id)->latest()->paginate(5, ['*'], 'balances_page');

        $opening = (float) optional(
            UserBalanceHistory::where('user_id', $user->id)
                ->where('change_type', 'opening')
                ->oldest()
                ->first()
        )->change_amount;
        $directBalance = (float) ($user->amount ?? 0);
        $transferBalance = 0.0;
        $currentBalance = $directBalance;

        return view('admin.users.show', [
            'user' => $user,
            'expenses' => $expenses,
            'totalDebited' => $totalDebited,
            'transfers' => $transfers,
            'totalTransfers' => $totalTransfers,
            'totalTransfersSent' => $totalTransfersSent,
            'balanceHistories' => $balanceHistories,
            'opening' => $opening,
            'directBalance' => $directBalance,
            'transferBalance' => $transferBalance,
            'currentBalance' => $currentBalance,
        ]);
    }

    public function edit($id): View
    {
        $user = User::with(['role', 'projects'])->findOrFail($id);

        return view('admin.users.edit', [
            'user'  => $user,
            'roles' => Role::where('id', '!=', 5)
                            ->orderBy('name')
                            ->get(),
        ]);
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'        => ['required', 'string', 'max:255', 'regex:/^[A-Za-z ]+$/'],
            'email'       => ['required', 'email', 'unique:users,email,' . $user->id],
            'mobile'      => ['nullable', 'regex:/^\d{10}$/'],
            'role_id'     => ['required', 'exists:roles,id'],
            'status'      => ['required', 'in:0,1'],
            'note'        => ['nullable', 'string', 'max:1000'],
            'password'    => ['nullable', Password::min(8)->mixedCase()->numbers()->symbols(), 'confirmed'],
            'amount'      => ['nullable', 'numeric', 'min:0'],
        ]);

        $data = [
            'name'    => $request->name,
            'email'   => $request->email,
            'mobile'  => $request->mobile,
            'role_id' => $request->role_id,
            'status'  => $request->status,
            'note'    => $request->note,
            'amount'  => $request->amount,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $original = $user->getOriginal();
        $user->update($data);
        $user->assignRole((int) $request->role_id);

        $changes = [];
        foreach ($data as $field => $newVal) {
            if ($field === 'password') continue;
            if (isset($original[$field]) && (string) $original[$field] !== (string) $newVal) {
                $changes[$field] = ['old' => $original[$field], 'new' => $newVal];
            }
        }

        // amount change: store balance history
        $oldAmount = isset($original['amount']) ? (float)$original['amount'] : 0.0;
        $newAmount = (float)$user->amount;
        $this->balanceService->recordAdjustment($user, $oldAmount, $newAmount, Auth::id());

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
        $query = User::with(['role', 'projects']);

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
        
        $excludedRoleIds = Role::whereIn('name', ['vendor', 'customer'])->pluck('id');
        $users = $query->whereNotIn('role_id', $excludedRoleIds)
            ->offset($start)
            ->limit($limit)
            ->orderBy('id', 'desc')
            ->get();

        $data = [];

        $auth = auth()->user();
        $canViewUser = $auth?->can('user-view') ?? false;
        $canEditUser = $auth?->can('user-edit') ?? false;

        foreach ($users as $i => $u) {

            $viewUrl = route('users.show', $u->id);
            $editUrl = route('users.edit', $u->id);
            // $deleteUrl = route('users.destroy', $u->id);

            $actionHtml  = '<div class="btn-group">';
            $actionHtml .= '
                            <i class="fas fa-ellipsis-v" data-toggle="dropdown" style="cursor:pointer;"></i>
                            <div class="dropdown-menu dropdown-menu-right" style="min-width: 50px; padding: 0;">';

            if ($canViewUser) {
                $actionHtml .= '<a href="' . $viewUrl . '" class="table-action-btn is-view" title="View"><i class="fas fa-eye"></i></a>';
            }
            if ($canEditUser) {
                $actionHtml .= '<a href="' . $editUrl . '" class="table-action-btn is-edit" title="Edit"><i class="fas fa-edit"></i></a>';
            }
            // $actionHtml .= '<form method="POST" action="' . $deleteUrl . '" class="table-action-form">';
            // $actionHtml .= '<input type="hidden" name="_token" value="' . csrf_token() . '">';
            // $actionHtml .= '<input type="hidden" name="_method" value="DELETE">';
            // $actionHtml .= '<button type="submit" class="table-action-btn is-delete deleteButton" title="Delete"><i class="fas fa-trash"></i></button>';
            // $actionHtml .= '</form>';
            $actionHtml .= '</div></div>';

            $avatarHtml = '<div class="user-name-cell">'
                // . '<img src="' . e($u->profile_image_url) . '" alt="' . e($u->name) . '" class="user-list-avatar">'
                . '<span>' . e($u->name) . '</span>'
                . '</div>';

            $data[] = [
                'id'      => $start + $i + 1,
                'name'    => $avatarHtml,
                'email'   => $u->email,
                'mobile'  => $u->mobile,
                'project' => $u->assignedProjectNames() ?: '—',
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
