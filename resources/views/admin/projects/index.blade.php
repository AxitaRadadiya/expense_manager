@extends('admin.layouts.app')
@section('title', 'Projects')

@section('content')
<div class="page-hero">
  <div class="orb"></div>
  <div class="container-fluid" style="position:relative;z-index:2;">
    <h1><i class="fas fa-folder-open mr-2" style="color:rgba(255,255,255,.85);font-size:1.1rem;"></i>Projects</h1>
    <p>Organize active workspaces, timelines, and assigned users with one consistent model UI.</p>
  </div>
</div>

<div class="pull-card">
  <div class="container-fluid" style="padding:0;">
    <div class="main-card table-card">
      <div class="main-card-head">
        <div class="main-card-title">
          <i class="fas fa-list"></i> All Projects
        </div>
        <a href="{{ route('projects.create') }}" class="btn-create">
          <i class="fas fa-plus"></i> New Project
        </a>
      </div>

      <div class="main-card-body">
        <div class="table-responsive">
          <table id="projectsTable" class="table table-hover w-100">
            <thead>
              <tr>
                <th>#</th>
                <th>Project Name</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Assigned Users</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
