@extends('admin.layouts.app')
@section('title', 'Users')

@section('content')
<div class="page-hero">
  <div class="orb"></div>
  <div class="container-fluid" style="position:relative;z-index:2;">
    <h1><i class="fas fa-users mr-2" style="color:rgba(255,255,255,.85);font-size:1.1rem;"></i>Users</h1>
    <p>Manage all system users, roles and access.</p>
  </div>
</div>

<div class="pull-card">
  <div class="container-fluid" style="padding:0;">
    @if(session('success'))
      <div class="alert-success-custom mt-3">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
      </div>
    @endif

    <div class="main-card table-card">
      <div class="main-card-head">
        <div class="main-card-title">
          <i class="fas fa-list"></i> All Users
          <span class="count-badge">{{ $users->total() }}</span>
        </div>
        <a href="{{ route('users.create') }}" class="btn-create">
          <i class="fas fa-plus"></i> New User
        </a>
      </div>

      <div class="main-card-body">
        <div class="table-responsive">
          <table id="userTable" class="table table-hover w-100">
            <thead>
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
</div>
@endsection
