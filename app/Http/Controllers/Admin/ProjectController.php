<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        $projects = Project::latest()->paginate(15);
        return view('admin.projects.index', compact('projects'));
    }

    public function create(): View
    {
        return view('admin.projects.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'required|string|max:50',
            'amount' => 'nullable|numeric',
            'note' => 'nullable|string',
        ]);

        Project::create($data);

        return redirect()->route('projects.index')->with('success', 'Project created.');
    }

    public function show(Project $project): View
    {
        return view('admin.projects.show', compact('project'));
    }

    public function edit(Project $project): View
    {
        return view('admin.projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'required|string|max:50',
            'amount' => 'nullable|numeric',
            'note' => 'nullable|string',
        ]);

        $project->update($data);

        return redirect()->route('projects.index')->with('success', 'Project updated.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted.');
    }

    public function list(Request $request)
    {
        $columns = [
            0 => 'id',
            1 => 'name',
            2 => 'action',
        ];

        $totalData     = Project::count();
        $totalFiltered = $totalData;
        $limit         = $request->input('length', 10);
        $start         = $request->input('start', 0);
        $orderIndex    = $request->input('order.0.column', 0);
        $order         = $columns[$orderIndex] ?? 'id';
        $dir           = $request->input('order.0.dir', 'desc');
        $search        = $request->input('search.value');

        $query = Project::query();

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
                $nestedData['status']     = ucfirst($project->status);
                $nestedData['amount']     = $project->amount ? number_format($project->amount, 2) : '';

                $actions = '<div class="btn-group">';

                if (auth()->user()) {
                    $actions .= '<a href="' . route('projects.edit', $project->id) . '" class="btn-sm btn-outline-primary"><i class="fa fa-edit"></i></a>';
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
}
