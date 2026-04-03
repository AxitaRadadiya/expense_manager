<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Credit;
use App\Models\Project;
use App\Services\BalanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CreditController extends Controller
{
    public function __construct(protected BalanceService $balanceService)
    {
        $this->middleware('auth');
    }

    public function index(): View|RedirectResponse
    {
        $auth = auth()->user();

        if (! $this->canManageCredits($auth)) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized to view credits.');
        }

        $credits = Credit::with(['project', 'user'])
            ->when($auth->hasRole('owner') && ! $auth->hasRole('super-admin'), function ($query) use ($auth) {
                $query->whereIn('projects_id', $auth->assignedProjectIds());
            })
            ->latest()
            ->get();

        return view('admin.credit.index', compact('credits'));
    }

    public function create(): View|RedirectResponse
    {
        $auth = auth()->user();

        if (! $this->canManageCredits($auth)) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized to add credit.');
        }

        $projects = $this->allowedProjects($auth);
        $categories = Category::orderBy('name')->get();

        return view('admin.credit.create', compact('projects', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $auth = auth()->user();

        if (! $this->canManageCredits($auth)) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized to add credit.');
        }

        $data = $request->validate([
            'projects_id' => 'required|exists:projects,id',
            'credit_date' => 'required|date|after_or_equal:today',
            'category' => 'required|string|max:255',
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
            'category.required' => 'Please select a credit category.',
            'amount.required' => 'Please enter the amount.',
            'amount.min' => 'The amount must be greater than 0.',
        ]);

        $allowedProjectIds = $this->allowedProjects($auth)->pluck('id')->map(fn ($id) => (int) $id)->all();

        if (! in_array((int) $data['projects_id'], $allowedProjectIds, true)) {
            return redirect()->back()->withInput()->withErrors([
                'projects_id' => 'You can only add credit for your assigned project.',
            ]);
        }

        if ($request->hasFile('bill')) {
            $billFile = $request->file('bill');
            $fileName = time() . '_' . $billFile->getClientOriginalName();
            $billFile->storeAs('credit/bill', $fileName, 'public');
            $data['bill_path'] = 'credit/bill/' . $fileName;
            $data['bill_original_name'] = $billFile->getClientOriginalName();
        }

        unset($data['bill']);
        $data['description'] = $data['description'] ?? '';
        $data['reference_number'] = $data['reference_number'] ?? '';
        $data['note'] = $data['note'] ?? '';
        $data['status'] = $data['status'] ?? 'pending';

        $this->balanceService->createCredit($auth, $data);

        return redirect()->route('credit.index')->with('success', 'Credit created successfully.');
    }

    public function show(Credit $credit): View|RedirectResponse
    {
        $auth = auth()->user();

        if (! $this->canAccessCredit($auth, $credit)) {
            return redirect()->route('credit.index')->with('error', 'Unauthorized to view this credit.');
        }

        $credit->load(['project', 'user']);

        return view('admin.credit.view', compact('credit'));
    }

    public function edit(Credit $credit): View|RedirectResponse
    {
        $auth = auth()->user();

        if (! $this->canAccessCredit($auth, $credit)) {
            return redirect()->route('credit.index')->with('error', 'Unauthorized to edit this credit.');
        }

        $credit->load(['project', 'user']);
        $projects = $this->allowedProjects($auth);
        $categories = Category::orderBy('name')->get();

        return view('admin.credit.edit', compact('credit', 'projects', 'categories'));
    }

    public function update(Request $request, Credit $credit): RedirectResponse
    {
        $auth = auth()->user();

        if (! $this->canAccessCredit($auth, $credit)) {
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
            'category' => 'required|string|max:255',
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
            'category.required' => 'Please select a credit category.',
            'amount.required' => 'Please enter the amount.',
            'amount.min' => 'The amount must be greater than 0.',
        ]);

        $allowedProjectIds = $this->allowedProjects($auth)->pluck('id')->map(fn ($id) => (int) $id)->all();

        if (! in_array((int) $data['projects_id'], $allowedProjectIds, true)) {
            return redirect()->back()->withInput()->withErrors([
                'projects_id' => 'You can only update credit for your assigned project.',
            ]);
        }

        if ($request->hasFile('bill')) {
            if ($credit->bill_path && Storage::disk('public')->exists($credit->bill_path)) {
                Storage::disk('public')->delete($credit->bill_path);
            }

            $billFile = $request->file('bill');
            $fileName = time() . '_' . $billFile->getClientOriginalName();
            $billFile->storeAs('credit/bill', $fileName, 'public');
            $data['bill_path'] = 'credit/bill/' . $fileName;
            $data['bill_original_name'] = $billFile->getClientOriginalName();
        }

        unset($data['bill']);
        $data['description'] = $data['description'] ?? '';
        $data['reference_number'] = $data['reference_number'] ?? '';
        $data['note'] = $data['note'] ?? '';
        $credit->update($data);

        return redirect()->route('credit.index')->with('success', 'Credit updated successfully.');
    }

    public function list(Request $request)
    {
        $auth = auth()->user();

        if (! $this->canManageCredits($auth)) {
            return response()->json([
                'draw' => intval($request->input('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ]);
        }

        $baseQuery = Credit::with(['project', 'user']);

        if ($auth->hasRole('owner') && ! $auth->hasRole('super-admin')) {
            $baseQuery->whereIn('projects_id', $auth->assignedProjectIds());
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
        $data = $credits->map(function ($credit, $i) use ($start) {
            return [
                'id' => $start + $i + 1,
                'project' => e(optional($credit->project)->name ?? '-'),
                'credit_date' => optional($credit->credit_date)?->format('d M Y') ?? '-',
                'amount' => '<span class="text-success font-weight-bold">Rs. ' . number_format((float) $credit->amount, 2) . '</span>',
                'created_by' => e(optional($credit->user)->name ?? '-'),
                'note' => e(filled($credit->category) ? $credit->category : '-'),
                'action' => '<div class="btn-group"><a href="' . route('credit.show', $credit->id) . '" class="btn-sm text-info" title="View"><i class="fa fa-eye"></i></a>&nbsp;<a href="' . route('credit.edit', $credit->id) . '" class="btn-sm text-primary" title="Edit"><i class="fa fa-edit"></i></a></div>',
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw', 1)),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }

    protected function canManageCredits($user): bool
    {
        return (bool) ($user && method_exists($user, 'hasRole') && $user->hasRole(['super-admin', 'owner']));
    }

    protected function canAccessCredit($user, Credit $credit): bool
    {
        if (! $this->canManageCredits($user)) {
            return false;
        }

        if ($user->hasRole('super-admin')) {
            return true;
        }

        return in_array((int) $credit->projects_id, $user->assignedProjectIds(), true);
    }

    protected function allowedProjects($user): Collection
    {
        if (! $user) {
            return collect();
        }

        if ($user->hasRole('super-admin')) {
            return Project::orderBy('name')->get();
        }

        return Project::whereIn('id', $user->assignedProjectIds())->orderBy('name')->get();
    }
}
