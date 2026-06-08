<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        $projects = Project::withCount('users')->latest()->paginate(15);
        return view('admin.projects.index', compact('projects'));
    }

    public function create(): View
    {
        $excludedRoleIds = Role::whereIn('name', ['vendor', 'customer'])->pluck('id');

        $users = User::whereNotIn('role_id', $excludedRoleIds)
                        ->orderBy('name')
                        ->get();

        $customers = User::whereHas('role', function ($q) {
            $q->where('name', 'customer');
        })->orderBy('name')->get();

        return view('admin.projects.create', compact('users', 'customers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'nullable|string|max:50',
            'amount' => 'nullable|numeric',
            'note' => 'nullable|string',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'integer|exists:users,id',
            'customer_ids' => 'nullable|array',
            'customer_ids.*' => 'integer|exists:users,id',
        ]);

        $data['status'] = $data['status'] ?? 'pending';
        $userIds = collect($request->input('user_ids', []))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $customerIds = collect($request->input('customer_ids', []))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $allUserIds = collect($userIds)
            ->merge($customerIds)
            ->unique()
            ->values()
            ->all();

        $project = Project::create(collect($data)->except(['user_ids', 'customer_ids'])->all());
        $project->users()->sync($allUserIds);
        $this->syncPrimaryProjects($allUserIds);

        return redirect()->route('projects.index')->with('success', 'Project created.');
    }

    public function show(Project $project): View
    {
        $project->load(['users.role']);
        return view('admin.projects.show', compact('project'));
    }

    public function edit(Project $project): View
    {
        $project->load('users');
        $excludedRoleIds = Role::whereIn('name', ['vendor', 'customer'])->pluck('id');

        $users = User::whereNotIn('role_id', $excludedRoleIds)
                        ->orderBy('name')
                        ->get();

        $customers = User::whereHas('role', function ($q) {
            $q->where('name', 'customer');
        })->orderBy('name')->get();

        return view('admin.projects.edit', compact('project', 'users', 'customers'));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'nullable|string|max:50',
            'amount' => 'nullable|numeric',
            'note' => 'nullable|string',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'integer|exists:users,id',
            'customer_ids' => 'nullable|array',
            'customer_ids.*' => 'integer|exists:users,id',
        ]);

        $data['status'] = $data['status'] ?? $project->status;
        $userIds = collect($request->input('user_ids', []))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $customerIds = collect($request->input('customer_ids', []))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $allUserIds = collect($userIds)
            ->merge($customerIds)
            ->unique()
            ->values()
            ->all();

        $affectedUserIds = $project->users()->pluck('users.id')
            ->map(fn ($id) => (int) $id)
            ->merge($allUserIds)
            ->unique()
            ->values()
            ->all();

        $project->update(collect($data)->except(['user_ids', 'customer_ids'])->all());
        $project->users()->sync($allUserIds);
        $this->syncPrimaryProjects($affectedUserIds);

        return redirect()->route('projects.index')->with('success', 'Project updated.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $affectedUserIds = $project->users()->pluck('users.id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $project->delete();
        $this->syncPrimaryProjects($affectedUserIds);

        return redirect()->route('projects.index')->with('success', 'Project deleted.');
    }

    public function list(Request $request)
    {
        $columns = [
            0 => 'id',
            1 => 'name',
            2 => 'start_date',
            3 => 'end_date',
            4 => 'customers_count',
            5 => 'users_count',
            6 => 'action',
        ];

        $totalData     = Project::count();
        $totalFiltered = $totalData;
        $limit         = $request->input('length', 10);
        $start         = $request->input('start', 0);
        $orderIndex    = $request->input('order.0.column', 0);
        $order         = $columns[$orderIndex] ?? 'id';
        $dir           = $request->input('order.0.dir', 'desc');
        $search        = $request->input('search.value');

        $query = Project::withCount([
            'users as customers_count' => function ($q) {
                $q->whereHas('role', function ($r) {
                    $r->where('name', 'customer');
                });
            },
            'users as users_count' => function ($q) {
                $q->whereHas('role', function ($r) {
                    $r->where('name', '<>', 'customer');
                });
            },
        ]);

        if (!empty($search)) {
            $query->where('name', 'like', "%$search%");
            $totalFiltered = $query->count();
        }

        $projects = $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        $data = [];
        if ($projects->isNotEmpty()) {
            $i = $start + 1;
            foreach ($projects as $project) {
                $nestedData               = [];
                $nestedData['id']         = $project->id;
                $nestedData['name']       = $project->name;
                $nestedData['start_date'] = $project->start_date ? $project->start_date->format('d-m-Y') : '';
                $nestedData['end_date']   = $project->end_date ? $project->end_date->format('d-m-Y') : '';
                $nestedData['customers_count'] = (int) ($project->customers_count ?? 0);
                $nestedData['users_count'] = (int) ($project->users_count ?? 0);
                // $nestedData['status']     = ucfirst($project->status);
                // $nestedData['amount']     = $project->amount ? number_format($project->amount, 2) : '';

                $auth = auth()->user();
                $canViewProject = $auth?->can('project-view') ?? false;
                $canEditProject = $auth?->can('project-edit') ?? false;
                $canDeleteProject = $auth?->can('project-delete') ?? false;

                $actions = '<div class="btn-group">';
                $actions .= '
                            <i class="fas fa-ellipsis-v" data-toggle="dropdown" style="cursor:pointer;"></i>
                            <div class="dropdown-menu dropdown-menu-right" style="min-width: 50px; padding: 0;">';

                if ($canViewProject) {
                    $actions .= '<a href="' . route('projects.show', $project->id) . '" class="table-action-btn is-view" title="View"><i class="fa fa-eye"></i></a>';
                }
                if ($canEditProject) {
                    $actions .= '<a href="' . route('projects.edit', $project->id) . '" class="table-action-btn is-edit" title="Edit"><i class="fa fa-edit"></i></a>';
                }

                if ($canDeleteProject) {
                    $actions .= '
                        <form action="' . route('projects.destroy', $project->id) . '" method="POST" class="table-action-form deleteForm">'
                        . csrf_field() .
                        '<input type="hidden" name="_method" value="DELETE">'
                        . '<button type="submit" class="table-action-btn is-delete deleteButton">'
                        . '<i class="fa fa-trash"></i>'
                        . '</button>'
                        . '</form>';
                }

                $actions .= '</div></div>';

                $nestedData['action'] = $actions;
                $data[] = $nestedData;
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

    protected function syncPrimaryProjects(array $userIds): void
    {
        if (empty($userIds)) {
            return;
        }

        User::with('projects:id')
            ->whereIn('id', $userIds)
            ->get()
            ->each(function (User $user) {
                $primaryProjectId = $user->projects
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id)
                    ->sort()
                    ->values()
                    ->first();

                if ((int) ($user->project_id ?? 0) !== (int) ($primaryProjectId ?? 0)) {
                    $user->forceFill(['project_id' => $primaryProjectId])->save();
                }
            });
    }

    public function getProjects($userId)
    {
        $projects = Project::whereHas('users', function ($query) use ($userId) {
            $query->where('users.id', $userId);
        })
        ->select('id', 'name')
        ->get();

        return response()->json($projects);
    }
}