@extends('admin.layouts.app')
@section('title', 'Roles')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mt-3 mb-4">
      <div class="d-flex justify-content-between align-items-center w-100">
        <h1><i class="mr-2 text-teal"></i>Roles</h1>
      </div>
    </div>
  </div>
</div>
    <ul class="nav nav-pills mb-3">
        <li class="nav-item mr-2">
          <a class="nav-link @if(request()->routeIs('users.*')) active @endif" href="{{ route('users.index') }}">Users</a>
        </li>
        <li class="nav-item">
          <a class="nav-link @if(request()->routeIs('roles.*')) active @endif" href="{{ route('roles.index') }}">Roles</a>
        </li>
      </ul>
<div class="pull-card">
  <div class="card card-outline card-primary shadow-sm">
    <div class="main-card table-card">
      <div class="main-card-body">
     
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h5 class="mb-0">All Roles</h5>
          <div></div>
          <a href="{{ route('roles.create') }}" class="btn-create">
            <i class="fas fa-plus"></i> New Role
          </a>
        </div>

        <div class="table-responsive">
          <table id="roleTable" class="table table-hover w-100">
            <thead class="thead">
              <tr>
                <th>#</th>
                <th>Role Name</th>
                <th>Actions</th>
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
