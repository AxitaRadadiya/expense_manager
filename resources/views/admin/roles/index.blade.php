@extends('admin.layouts.app')
@section('title', 'Roles')

@section('content')

{{-- Page Header --}}
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">
          <i class="fas fa-shield-alt mr-2 text-warning"></i>Roles
        </h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">Roles</li>
        </ol>
      </div>
    </div>
  </div>
</div>

{{-- Content --}}
<section class="content">
  <div class="container-fluid">

    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">
          <span>&times;</span>
        </button>
      </div>
    @endif

    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-list mr-2"></i>All Roles
        </h3>
        <div class="card-tools">
         
            <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm">
              <i class="fas fa-plus mr-1"></i> New Role
            </a>
         
        </div>
      </div>

      <div class="card-body">
        <table id="roleTable" class="table table-bordered table-striped table-hover">
          <thead>
            <tr>
              <th width="5%">#</th>
              <th width="20%">Role Name</th>
              <th width="10%">Actions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>

  </div>
</section>
@endsection