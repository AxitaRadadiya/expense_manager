@extends('admin.layouts.app')
@section('title', 'Users')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mt-3">
      <h1><i class="mr-2 text-teal"></i>Users</h1>
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

<div class="container-fluid">
  @if(session('success'))
    <div class="alert-success-custom mt-3">
      <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
  @endif

  <div class="card card-outline card-primary shadow-sm">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">All Users</h5>
        <div></div>
        <a href="{{ route('users.create') }}" class="btn-create">
          <i class="fas fa-plus"></i> New User
        </a>
      </div>
      <div class="table-responsive">
        <table id="userTable" class="table table-hover w-100">
          <thead class="thead">
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Email</th>
              <th>Mobile</th>
              <th>Projects</th>
              <th>Amount</th>
              <th>Role</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection