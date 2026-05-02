@extends('admin.layouts.app')
@section('title', 'Create Role')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><i class="fas fa-plus-circle mr-2 text-primary"></i>Create Role</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
          <li class="breadcrumb-item active">Create</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <form action="{{ route('roles.store') }}" method="POST">
      @csrf

      <div class="card card-outline card-primary shadow-sm">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-tag mr-2"></i>Role Info</h3>
          <div class="card-tools">
            <a href="{{ route('roles.index') }}" class="btn btn-default btn-sm">
              <i class="fas fa-arrow-left mr-1"></i>Back
            </a>
          </div>
        </div>
        <div class="card-body">
          <p class="text-uppercase text-muted font-weight-bold mb-3" style="font-size:.7rem;letter-spacing:1.4px;">
            <i class="fas fa-user-shield mr-1"></i> Basic Info
          </p>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="name" class="font-weight-bold">Role Name <span class="text-danger">*</span></label>
                <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Manager, Editor, Viewer" required>
                @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card card-outline card-primary shadow-sm">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fas fa-key mr-2"></i>Permissions
            <span class="badge badge-secondary ml-1">{{ $permissions->flatten()->count() }} total</span>
          </h3>
          <div class="card-tools">
            <button type="button" class="btn btn-primary btn-sm mr-1" onclick="selectAll(true)">
              <i class="fas fa-check-double mr-1"></i>Select All
            </button>
            <button type="button" class="btn btn-default btn-sm" onclick="selectAll(false)">
              <i class="fas fa-times mr-1"></i>Clear All
            </button>
          </div>
        </div>
        <div class="card-body">
          @if($permissions->isNotEmpty())
            <div class="table-responsive">
              <table class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>Module</th>
                    <th class="text-center">View</th>
                    <th class="text-center">Create</th>
                    <th class="text-center">Edit</th>
                    <th class="text-center">Delete</th>
                    <th class="text-center">All</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($permissions as $module => $perms)
                    @php
                      // map action => permission id (if present)
                      $map = ['view' => null, 'create' => null, 'edit' => null, 'delete' => null];
                      foreach ($perms as $p) {
                          $name = strtolower($p->name);
                          if (str_ends_with($name, '-view') || str_contains($name, 'view')) $map['view'] = $p->id;
                          if (str_ends_with($name, '-create') || str_contains($name, 'create')) $map['create'] = $p->id;
                          if (str_ends_with($name, '-edit') || str_contains($name, 'edit')) $map['edit'] = $p->id;
                          if (str_ends_with($name, '-delete') || str_contains($name, 'delete')) $map['delete'] = $p->id;
                      }
                      $rowId = 'perm_row_' . \'\'' . md5($module);
                    @endphp
                    <tr>
                      <td class="align-middle">{{ $module }}</td>
                      <td class="text-center align-middle">
                        @if($map['view'])
                          <input class="perm-chk module-{{ md5($module) }}" type="checkbox" name="permissions[]" value="{{ $map['view'] }}" id="perm_{{ $map['view'] }}" {{ in_array($map['view'], old('permissions', [])) ? 'checked' : '' }}>
                        @endif
                      </td>
                      <td class="text-center align-middle">
                        @if($map['create'])
                          <input class="perm-chk module-{{ md5($module) }}" type="checkbox" name="permissions[]" value="{{ $map['create'] }}" id="perm_{{ $map['create'] }}" {{ in_array($map['create'], old('permissions', [])) ? 'checked' : '' }}>
                        @endif
                      </td>
                      <td class="text-center align-middle">
                        @if($map['edit'])
                          <input class="perm-chk module-{{ md5($module) }}" type="checkbox" name="permissions[]" value="{{ $map['edit'] }}" id="perm_{{ $map['edit'] }}" {{ in_array($map['edit'], old('permissions', [])) ? 'checked' : '' }}>
                        @endif
                      </td>
                      <td class="text-center align-middle">
                        @if($map['delete'])
                          <input class="perm-chk module-{{ md5($module) }}" type="checkbox" name="permissions[]" value="{{ $map['delete'] }}" id="perm_{{ $map['delete'] }}" {{ in_array($map['delete'], old('permissions', [])) ? 'checked' : '' }}>
                        @endif
                      </td>
                      <td class="text-center align-middle">
                        <button type="button" class="btn btn-xs btn-outline-primary row-toggle-btn" data-module="{{ md5($module) }}">All</button>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="text-center text-muted py-4">
              <i class="fas fa-key fa-2x mb-2 d-block"></i>
              No permissions found. Create permissions first.
            </div>
          @endif
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-shield-alt mr-1"></i>Create Role
          </button>
          <a href="{{ route('roles.index') }}" class="btn btn-default ml-2">
            <i class="fas fa-times mr-1"></i>Cancel
          </a>
        </div>
      </div>
    </form>
  </div>
</section>

<script>
function toggleGroup(btn) {
  const cardBody = btn.closest('.card').querySelector('.card-body');
  const checkboxes = cardBody.querySelectorAll('.perm-chk');
  const allChecked = [...checkboxes].every(c => c.checked);
  checkboxes.forEach(c => { c.checked = !allChecked; });
  btn.textContent = allChecked ? 'All' : 'None';
}
function selectAll(state) {
  document.querySelectorAll('.perm-chk').forEach(c => { c.checked = state; });
  document.querySelectorAll('.group-toggle-btn').forEach(b => { b.textContent = state ? 'None' : 'All'; });
}
</script>
<script>
document.addEventListener('click', function (e) {
  // row toggle button
  if (e.target && e.target.classList.contains('row-toggle-btn')) {
    var moduleKey = e.target.getAttribute('data-module');
    var checks = document.querySelectorAll('.module-' + moduleKey);
    var allChecked = Array.from(checks).every(function (c) { return c.checked; });
    checks.forEach(function (c) { c.checked = !allChecked; });
    e.target.textContent = allChecked ? 'All' : 'None';
  }
});

function selectAll(state) {
  document.querySelectorAll('.perm-chk').forEach(function(c) { c.checked = state; });
  document.querySelectorAll('.row-toggle-btn').forEach(function(b) { b.textContent = state ? 'None' : 'All'; });
}
</script>
@endsection
