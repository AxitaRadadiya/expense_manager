<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Credit;
use App\Models\Project;
use App\Models\UserBalanceHistory;
use App\Services\CreditService;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CreditController extends Controller
{
    public function __construct(
        protected CreditService $creditService,
        protected FileUploadService $fileUploadService
    ) {
        $this->middleware('auth');
    }

    public function index(): View|RedirectResponse
    {
        $auth = auth()->user();

        if (! $this->creditService->canManageCredits($auth)) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized to view credits.');
        }

        $credits = $this->creditService->getFilteredCredits($auth);

        return view('admin.credit.index', compact('credits'));
    }

    public function create(): View|RedirectResponse
    {
        $auth = auth()->user();

        if (! $this->creditService->canManageCredits($auth)) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized to add credit.');
        }

        $projects = $this->creditService->getAllowedProjects($auth);
        $categories = Category::orderBy('name')->get();

        return view('admin.credit.create', compact('projects', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $auth = auth()->user();

        if (! $this->creditService->canManageCredits($auth)) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized to add credit.');
        }

        $data = $request->validate([
            'projects_id' => 'required|exists:projects,id',
            'credit_date' => 'required|date|after_or_equal:today',
            'category' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'bill' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'payment_mode' => 'nullable|in:cash,online,cheque',
            'reference_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'note' => 'nullable|string',
        ], [
            'projects_id.required' => 'Please select a project.',
            'projects_id.exists' => 'The selected project is invalid.',
            'credit_date.required' => 'Please enter the credit date.',
            'credit_date.after_or_equal' => 'The credit date cannot be a past date.',
            'amount.required' => 'Please enter the amount.',
            'amount.min' => 'The amount must be greater than 0.',
        ]);

        // Validate project access
        if (! $this->creditService->canAccessProject($auth, (int) $data['projects_id'])) {
            return redirect()->back()->withInput()->withErrors([
                'projects_id' => 'You can only add credit for your assigned project.',
            ]);
        }

        if ($request->hasFile('bill')) {
            $billFile = $request->file('bill');
            $data['bill_path'] = $this->fileUploadService->storeFile($billFile, 'credit/bill');
            $data['bill_original_name'] = $billFile->getClientOriginalName();
        }

        unset($data['bill']);
        $data['description'] = $data['description'] ?? '';
        $data['reference_number'] = $data['reference_number'] ?? '';
        $data['note'] = $data['note'] ?? '';
        // Ensure category is present when creating a credit so DB insert
        // doesn't fail if the column is not nullable or migration
        // hasn't been run. Use empty string as a safe default.
        $data['category'] = $data['category'] ?? '';
        $data['status'] = $data['status'] ?? 'pending';

        $this->creditService->createCredit($auth, $data);

        return redirect()->route('credit.index')->with('success', 'Credit created successfully.');
    }

    public function show(Credit $credit): View|RedirectResponse
    {
        $auth = auth()->user();

        if (! $this->creditService->canAccessCredit($auth, $credit)) {
            return redirect()->route('credit.index')->with('error', 'Unauthorized to view this credit.');
        }

        $credit->load(['project', 'user']);

        return view('admin.credit.view', compact('credit'));
    }

    public function edit(Credit $credit): View|RedirectResponse
    {
        $auth = auth()->user();

        if (! $this->creditService->canAccessCredit($auth, $credit)) {
            return redirect()->route('credit.index')->with('error', 'Unauthorized to edit this credit.');
        }

        $credit->load(['project', 'user']);
        $projects = $this->creditService->getAllowedProjects($auth);
        $categories = Category::orderBy('name')->get();

        return view('admin.credit.edit', compact('credit', 'projects', 'categories'));
    }

    public function update(Request $request, Credit $credit): RedirectResponse
    {
        $auth = auth()->user();

        if (! $this->creditService->canAccessCredit($auth, $credit)) {
            return redirect()->route('credit.index')->with('error', 'Unauthorized to edit this credit.');
        }

        $data = $request->validate([
            'projects_id' => 'required|exists:projects,id',
            'credit_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($credit) {
                    $selectedDate = \Carbon\Carbon::parse($value)->toDateString();
                    $today = now()->toDateString();
                    $originalDate = optional($credit->credit_date)?->toDateString()
                        ?? \Carbon\Carbon::parse($credit->credit_date)->toDateString();

                    if ($selectedDate < $today && $selectedDate !== $originalDate) {
                        $fail('The credit date cannot be a past date.');
                    }
                },
            ],
            'category' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'bill' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'payment_mode' => 'nullable|in:cash,online,cheque',
            'reference_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'note' => 'nullable|string',
        ], [
            'projects_id.required' => 'Please select a project.',
            'projects_id.exists' => 'The selected project is invalid.',
            'credit_date.required' => 'Please enter the credit date.',
            'amount.required' => 'Please enter the amount.',
            'amount.min' => 'The amount must be greater than 0.',
        ]);

        // Validate project access
        if (! $this->creditService->canAccessProject($auth, (int) $data['projects_id'])) {
            return redirect()->back()->withInput()->withErrors([
                'projects_id' => 'You can only update credit for your assigned project.',
            ]);
        }

        if ($request->hasFile('bill')) {
            if ($credit->bill_path) {
                $this->fileUploadService->deleteFile($credit->bill_path);
            }

            $billFile = $request->file('bill');
            $data['bill_path'] = $this->fileUploadService->storeFile($billFile, 'credit/bill');
            $data['bill_original_name'] = $billFile->getClientOriginalName();
        }

        unset($data['bill']);
        $data['description'] = $data['description'] ?? '';
        $data['reference_number'] = $data['reference_number'] ?? '';
        $data['note'] = $data['note'] ?? '';
        $credit->update($data);

        return redirect()->route('credit.index')->with('success', 'Credit updated successfully.');
    }

    public function destroy(Credit $credit): RedirectResponse
    {
        $auth = auth()->user();

        if (! $this->creditService->canAccessCredit($auth, $credit)) {
            return redirect()->route('credit.index')->with('error', 'Unauthorized to delete this credit.');
        }

        DB::transaction(function () use ($credit, $auth) {
            $credit->loadMissing('user');

            if ($credit->user) {
                $balanceBefore = round((float) ($credit->user->amount ?? 0), 2);
                $creditAmount = round((float) ($credit->amount ?? 0), 2);
                $balanceAfter = round($balanceBefore - $creditAmount, 2);

                $credit->user->update([
                    'amount' => $balanceAfter,
                ]);

                UserBalanceHistory::create([
                    'user_id' => $credit->user->id,
                    'change_type' => 'credit_deleted',
                    'change_amount' => -$creditAmount,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'reference_type' => 'credit',
                    'reference_id' => $credit->id,
                    'created_by' => $auth?->id,
                    'note' => 'Credit deleted',
                ]);
            }

            $credit->delete();
        });

        if ($credit->bill_path) {
            $this->fileUploadService->deleteFile($credit->bill_path);
        }

        return redirect()->route('credit.index')->with('success', 'Credit deleted successfully.');
    }

    public function list(Request $request)
    {
        $auth = auth()->user();

        if (! $this->creditService->canManageCredits($auth)) {
            return response()->json([
                'draw' => intval($request->input('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ]);
        }

        $baseQuery = Credit::with(['project', 'user']);

        if (! $auth->hasRole('super-admin')) {
            $baseQuery->where('users_id', $auth->id);
        }

        $totalData = (clone $baseQuery)->count();

        if (! empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $baseQuery->where(function ($q) use ($search) {
                $q->where('category', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%")
                    ->orWhere('credit_date', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhere('payment_mode', 'like', "%{$search}%")
                    ->orWhere('note', 'like', "%{$search}%")
                    ->orWhereHas('project', function ($pq) use ($search) {
                        $pq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $totalFiltered = (clone $baseQuery)->count();
        $columns = [0 => 'id', 2 => 'credit_date', 3 => 'amount'];
        $orderColumnIndex = (int) $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc') === 'asc' ? 'asc' : 'desc';
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';

        $credits = $baseQuery
            ->orderBy($orderColumn, $orderDir)
            ->offset((int) $request->input('start', 0))
            ->limit((int) $request->input('length', 10))
            ->get();

        $start = (int) $request->input('start', 0);
        $canViewCredit = $auth?->can('credit-view') ?? false;
        $canEditCredit = $auth?->can('credit-edit') ?? false;
        $canDeleteCredit = $auth?->can('credit-delete') ?? false;

        $data = $credits->map(function ($credit, $i) use ($start, $canViewCredit, $canEditCredit, $canDeleteCredit) {
            $actions = '<div class="btn-group">';
            $actions .= '
                            <i class="fas fa-ellipsis-v" data-toggle="dropdown" style="cursor:pointer;"></i>
                            <div class="dropdown-menu dropdown-menu-right" style="min-width: 50px; padding: 0;">';

            if ($canViewCredit) {
                $actions .= '<a href="' . route('credit.show', $credit->id) . '" class="table-action-btn is-view" title="View"><i class="fa fa-eye"></i></a>';
            }

            if ($canEditCredit) {
                $actions .= '<a href="' . route('credit.edit', $credit->id) . '" class="table-action-btn is-edit" title="Edit"><i class="fa fa-edit"></i></a>';
            }

            if ($canDeleteCredit) {
                $actions .= '<form action="' . route('credit.destroy', $credit->id) . '" method="POST" class="table-action-form">' . csrf_field() . '<input type="hidden" name="_method" value="DELETE"><button type="button" class="table-action-btn is-delete deleteButton" title="Delete"><i class="fa fa-trash"></i></button></form>';
            }

            $actions .= '</div></div>';

            return [
                'id' => $start + $i + 1,
                'project' => e(optional($credit->project)->name ?? '-'),
                'credit_date' => optional($credit->credit_date)?->format('d-m-Y') ?? '-',
                'amount' => '<span class="text-success font-weight-bold">Rs. ' . number_format((float) $credit->amount, 2) . '</span>',
                'created_by' => e(optional($credit->user)->name ?? '-'),
                'category' => e(filled($credit->category) ? $credit->category : '-'),
                'note' => e(filled($credit->note) ? $credit->note : '-'),
                'action' => $actions,
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw', 1)),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }
}
