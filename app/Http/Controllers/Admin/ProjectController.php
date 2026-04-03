<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
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
        return view('admin.projects.create', [
            'users' => User::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'nullable|string|max:50',
            'amount' => 'nullable|numeric',
            'note' => 'nullable|string',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        $data['status'] = $data['status'] ?? 'pending';
        $userIds = collect($request->input('user_ids', []))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $project = Project::create(collect($data)->except(['user_ids'])->all());
        $project->users()->sync($userIds);
        $this->syncPrimaryProjects($userIds);

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

        return view('admin.projects.edit', [
            'project' => $project,
            'users' => User::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'nullable|string|max:50',
            'amount' => 'nullable|numeric',
            'note' => 'nullable|string',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        $data['status'] = $data['status'] ?? $project->status;
        $userIds = collect($request->input('user_ids', []))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
        $affectedUserIds = $project->users()->pluck('users.id')
            ->map(fn ($id) => (int) $id)
            ->merge($userIds)
            ->unique()
            ->values()
            ->all();

        $project->update(collect($data)->except(['user_ids'])->all());
        $project->users()->sync($userIds);
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
            4 => 'users_count',
            5 => 'action',
        ];

        $totalData     = Project::count();
        $totalFiltered = $totalData;
        $limit         = $request->input('length', 10);
        $start         = $request->input('start', 0);
        $orderIndex    = $request->input('order.0.column', 0);
        $order         = $columns[$orderIndex] ?? 'id';
        $dir           = $request->input('order.0.dir', 'desc');
        $search        = $request->input('search.value');

        $query = Project::withCount('users');

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
                $nestedData['start_date'] = $project->start_date ? $project->start_date->format('Y-m-d') : '';
                $nestedData['end_date']   = $project->end_date ? $project->end_date->format('Y-m-d') : '';
                $nestedData['users_count'] = (int) $project->users_count;
                // $nestedData['status']     = ucfirst($project->status);
                // $nestedData['amount']     = $project->amount ? number_format($project->amount, 2) : '';

                $actions = '<div class="btn-group">';

                if (auth()->user()) {
                    $actions .= '<a href="' . route('projects.show', $project->id) . '" class="btn-sm btn-outline-info mr-1" title="View"><i class="fa fa-eye"></i></a>';
                    $actions .= '<a href="' . route('projects.edit', $project->id) . '" class="btn-sm btn-outline-primary" title="Edit"><i class="fa fa-edit"></i></a>';
                }

                if (auth()->user()) {
                    $actions .= '
                        <form action="' . route('projects.destroy', $project->id) . '" method="POST" class="deleteForm" style="display:inline-block">'
                        . csrf_field() .
                        '<input type="hidden" name="_method" value="DELETE">'
                        . '<button type="submit" class="deleteButton border-0 bg-white text-danger ms-1">'
                        . '<i class="fa fa-trash"></i>'
                        . '</button>'
                        . '</form>';
                }

                $actions .= '</div>';

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
}
