@extends('admin.layouts.app')
@section('title', 'Projects')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mt-3">
      <h1><i class="mr-2 text-teal"></i>Project List</h1>
    </div>
  </div>
</div>


<div class="pull-card">
  <div class="container-fluid" style="padding:0;">
    <div class="main-card table-card">
      <div class="main-card-head">
        <a href="{{ route('projects.create') }}" class="btn-create">
          <i class="fas fa-plus"></i> New Project
        </a>
      </div>

      <div class="main-card-body">
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
</div>
@endsection
