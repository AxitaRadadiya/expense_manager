@extends('admin.layouts.app')
@section('title', 'Projects')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mt-3 mb-4">
      <h1><i class="mr-2 text-teal"></i>Project List</h1>
    </div>
  </div>
</div>


<div class="container-fluid">
  <div class="card card-outline card-primary shadow-sm">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">All Projects</h5>
        <div></div>
        <a href="{{ route('projects.create') }}" class="btn-create">
          <i class="fas fa-plus"></i> New Project
        </a>
      </div>

        <div class="table-responsive">
          <table id="projectsTable" class="table table-hover w-100">
            <thead class="thead">
              <tr>
                <th>#</th>
                <th>Project Name</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Assigned Users</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
    </div>
  </div>
</div>
@endsection
