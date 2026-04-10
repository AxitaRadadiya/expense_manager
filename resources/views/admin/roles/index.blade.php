@extends('admin.layouts.app')
@section('title', 'Roles')

@section('content')
<div class="page-hero">
  <div class="orb"></div>
  <div class="container-fluid" style="position:relative;z-index:2;">
    <h1><i class="fas fa-shield-alt mr-2" style="color:rgba(255,255,255,.85);font-size:1.1rem;"></i>Roles</h1>
    <p>Review system roles and permission structures with the same shared card and table styling.</p>
  </div>
</div>

<div class="pull-card">
  <div class="container-fluid" style="padding:0;">
    <div class="main-card table-card">
      <div class="main-card-head">
        <div class="main-card-title">
          <i class="fas fa-list"></i> All Roles
        </div>
  
      </div>

      <div class="main-card-body">
        <div class="table-responsive">
          <table id="roleTable" class="table table-hover w-100">
            <thead>
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
