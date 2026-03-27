<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Project;
use App\Models\User;
use App\Models\UserBalanceHistory;
use App\Models\Transfer;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Services\BalanceService;

class ExpenseController extends Controller
{
    public function __construct(protected BalanceService $balanceService)
    {
    }

    public function index(): View
    {
        $auth = auth()->user();

        if ($auth && method_exists($auth, 'hasRole') && $auth->hasRole('super-admin')) {
            $expense = Expense::with(['project', 'user'])->latest()->get();
        } else {
            $expense = Expense::with(['project', 'user'])
                ->where('users_id', $auth?->id ?? 0)
                ->latest()
                ->get();
        }

        return view('admin.expense.index', compact('expense'));
    }

    public function create(): View
    {
        $auth = auth()->user();

        // Superadmin can see all projects, others see only their assigned project (if any)
        if ($auth && method_exists($auth, 'hasRole') && $auth->hasRole('super-admin')) {
            $projects = Project::orderBy('name')->get();
        } else {
            if ($auth) {
                $projects = Project::whereIn('id', $auth->assignedProjectIds())->orderBy('name')->get();
            } else {
                $projects = collect();
            }
        }

        $users = User::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        return view('admin.expense.create', compact('projects', 'users', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            [
                'projects_id'  => 'required|exists:projects,id',
                'expense_date'     => 'required|date|before_or_equal:today',
                'amount'       => 'required|numeric|min:0',
                'bill'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'category'     => 'required|string|max:255',

                'payment_mode' => 'required|in:cash,online,cheque',
                'reference_number' => 'nullable',
                'description'      => 'nullable',
                'note'             => 'required|string',
                'status'           => 'nullable',
            ],
            [
                'projects_id.required'       => 'Please select a project.',
                'projects_id.exists'         => 'The selected project is invalid.',

                'expense_date.required'      => 'Please enter the expense date.',
                'expense_date.date'          => 'The expense date must be a valid date.',
                'expense_date.before_or_equal' => 'The expense date cannot be a future date.',

                'amount.required'            => 'Please enter the amount.',
                'amount.numeric'             => 'The amount must be a valid number.',
                'amount.min'                 => 'The amount must be at least 0.',

                'bill.file'             => 'The bill must be a valid file.',
                'bill.mimes'            => 'Only PDF, JPG, and PNG files are allowed.',
                'bill.max'              => 'The bill file size must not exceed 2MB.',
                'category.required'     => 'Please select an expense category.',
                'note.required'         => 'Please enter a note.',

            ]
        );

        $validated['users_id'] = auth()->id();

        if ($request->hasFile('bill')) {
            $billFile = $request->file('bill');
            $fileName = time() . '_' . $billFile->getClientOriginalName();

            $billFile->storeAs('expense/bill', $fileName, 'public');

            $validated['bill_path']          = 'expense/bill/' . $fileName;
            $validated['bill_original_name'] = $billFile->getClientOriginalName();
        }

        unset($validated['bill']);

        $validated['status']           = $validated['status'] ?? 'pending';

        // Ensure nullable string columns never pass null to a NOT NULL DB column
        $validated['description']      = $validated['description']      ?? '';
        $validated['reference_number'] = $validated['reference_number'] ?? '';

        // Ensure authenticated user
        $user = auth()->user();
        if (! $user) {
            return redirect()->back()->withErrors(['auth' => 'User must be authenticated'])->withInput();
        }

        try {
            $this->balanceService->createExpense($user, $validated);
        } catch (\Exception $e) {
            \Log::error('Expense balance flow failed: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['expense' => 'Expense could not be saved.']);
        }

        return redirect()->route('expense.index')
            ->with('success', 'Expense created successfully.');
    }

    public function show($id): View
    {
        $expense = Expense::with(['project', 'user'])->findOrFail($id);

        return view('admin.expense.view', compact('expense'));
    }

    public function edit($id): View
    {
        $expense    = Expense::findOrFail($id);
        $projects   = Project::orderBy('name')->get();
        $users      = User::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('admin.expense.edit', compact('expense', 'projects', 'users', 'categories'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $expense = Expense::findOrFail($id);

        $validated = $request->validate(
            [
                'projects_id'  => 'required|exists:projects,id',
                'expense_date'     => 'required|date|before_or_equal:today',
                'amount'       => 'required|numeric|min:0',
                'bill'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'category'     => 'required|string|max:255',
                'payment_mode'     => 'required|in:cash,online,cheque',
                'reference_number' => 'nullable',
                'description'      => 'nullable',
                'note'             => 'required|string',
            ],
            [
                'projects_id.required'         => 'Please select a project.',
                'projects_id.exists'           => 'The selected project is invalid.',

                'expense_date.required'        => 'Please enter the expense date.',
                'expense_date.date'            => 'The expense date must be a valid date.',
                'expense_date.before_or_equal' => 'The expense date cannot be a future date.',

                'amount.required'              => 'Please enter the amount.',
                'amount.numeric'               => 'The amount must be a valid number.',
                'amount.min'                   => 'The amount must be at least 0.',
                'bill.file'             => 'The bill must be a valid file.',
                'bill.mimes'            => 'Only PDF, JPG, and PNG files are allowed.',
                'bill.max'              => 'The bill file size must not exceed 2MB.',
                'category.required'     => 'Please select an expense category.',
                'payment_mode.required' => 'Please select a payment mode.',
                'note.required'         => 'Please enter a note.',

            ]
        );

        if ($request->hasFile('bill')) {

            // Delete old bill file if exists
            if ($expense->bill_path && Storage::disk('public')->exists($expense->bill_path)) {
                Storage::disk('public')->delete($expense->bill_path);
            }

            $billFile = $request->file('bill');
            $fileName = time() . '_' . $billFile->getClientOriginalName();

            $billFile->storeAs('expense/bill', $fileName, 'public');

            $validated['bill_path']          = 'expense/bill/' . $fileName;
            $validated['bill_original_name'] = $billFile->getClientOriginalName();

        } else {
            unset($validated['bill_path']);
            unset($validated['bill_original_name']);
        }

        // Remove 'bill' key — no such column in DB
        unset($validated['bill']);

        // Ensure nullable string columns never pass null to a NOT NULL DB column
        $validated['description']      = $validated['description']      ?? '';
        $validated['reference_number'] = $validated['reference_number'] ?? '';

        $expense->update($validated);

        return redirect()->route('expense.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        $expense = Expense::findOrFail($id);

        // Delete bill file from storage
        if ($expense->bill_path && Storage::disk('public')->exists($expense->bill_path)) {
            Storage::disk('public')->delete($expense->bill_path);
        }

        $expense->delete();

        return redirect()->route('expense.index')
            ->with('success', 'Expense deleted successfully.');
    }

    // ─── DataTables ───────────────────────────────────────────────────────────

    public function list(Request $request)
    {
        $columns = [
            0 => 'id',
            1 => 'expense_date',
            2 => 'category',
            3 => 'amount',
            4 => 'status',
            5 => 'created_at',
        ];

        $auth = auth()->user();
        $canEditExpense = $auth && method_exists($auth, 'hasRole') && $auth->hasRole('super-admin');

        // Base query respects permissions: super-admin sees all, others only their own
        $baseQuery = Expense::query();
        if (! ($auth && method_exists($auth, 'hasRole') && $auth->hasRole('super-admin'))) {
            $baseQuery->where('users_id', $auth?->id ?? 0);
        }

        $totalData     = $baseQuery->count();
        $totalFiltered = $totalData;
        $limit         = $request->input('length', 10);
        $start         = $request->input('start', 0);
        $orderIndex    = $request->input('order.0.column', 0);
        $order         = $columns[$orderIndex] ?? 'id';
        $dir           = $request->input('order.0.dir', 'desc');
        $search        = $request->input('search.value');

        $query = $baseQuery->with(['project', 'user']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('category',           'like', "%{$search}%")
                  ->orWhere('sub_category',     'like', "%{$search}%")
                  ->orWhere('description',      'like', "%{$search}%")
                  ->orWhere('amount',           'like', "%{$search}%")
                  ->orWhere('expense_date',     'like', "%{$search}%")
                  ->orWhere('payment_mode',     'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('status',           'like', "%{$search}%")
                  ->orWhereHas('project', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name',  'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });

            $totalFiltered = $query->count();
        }

        $expenses = $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        $data = [];

        if ($expenses->isNotEmpty()) {
            $i = $start + 1;

            foreach ($expenses as $item) {

                // ── Status Badge ──────────────────────────────────────────────
                $statusClass = match($item->status) {
                    'approved' => 'badge-success',
                    'rejected' => 'badge-danger',
                    default    => 'badge-warning',
                };
                $statusBadge = '<span class="badge ' . $statusClass . '">'
                             . ucfirst($item->status)
                             . '</span>';

                // ── Action Buttons ────────────────────────────────────────────
                $actions  = '<div class="btn-group">';
                $actions .= '<a href="' . route('expense.show', $item->id) . '"
                                class="btn-sm text-info" title="View">
                                <i class="fa fa-eye"></i>
                            </a>&nbsp;';
                if ($canEditExpense) {
                    $actions .= '<a href="' . route('expense.edit', $item->id) . '"
                                    class="btn-sm text-primary" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </a>&nbsp;';
                }
                $actions .= '
                    <form action="' . route('expense.destroy', $item->id) . '"
                        method="POST"
                        data-delete-type="expense"
                        class="d-inline">
                        ' . csrf_field() . '
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="button"
                                class="deleteButton border-0 bg-white text-danger btn-sm"
                                title="Delete">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>';
                $actions .= '</div>';

                $data[] = [
                    'id'           => $i,
                    'project'      => optional($item->project)->name ?? '—',
                    'user'         => optional($item->user)->name    ?? '—',
                    'expense_date' => \Carbon\Carbon::parse($item->expense_date)->format('d M, Y'),
                    'amount'       => '₹ ' . number_format((float) $item->amount, 2),
                    'payment_mode' => $item->payment_mode
                                    ? ucfirst(str_replace('_', ' ', $item->payment_mode))
                                    : '—',
                    'status'       => $statusBadge,
                    'action'       => $actions,
                ];

                $i++;
            }
        }

        return response()->json([
            'draw'            => intval($request->input('draw')),
            'recordsTotal'    => intval($totalData),
            'recordsFiltered' => intval($totalFiltered),
            'data'            => $data,
        ]);
    }
}
