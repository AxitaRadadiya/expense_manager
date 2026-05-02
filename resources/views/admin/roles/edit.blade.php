@extends('admin.layouts.app')
@section('title', 'Edit Role')

@section('content')
<div class="content-header">
  <div class="container-fluid-80">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><i class="mr-2 text-primary"></i>Edit Role</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
          <li class="breadcrumb-item active">Edit</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid-80">
    <form action="{{ route('roles.update', $role->id) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="card card-outline card-primary shadow-sm">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-tag mr-2"></i>Role Info</h3>
          <div class="card-tools">
            <span class="badge badge-primary mr-2"><i class="fas fa-shield-alt mr-1"></i>{{ $role->name }}</span>
            <a href="{{ route('roles.index') }}" class="btn-cancel">
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
                <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $role->name) }}" required readonly>
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
            <span class="badge badge-success ml-1" id="assignedBadge">{{ count($assignedIds) }} assigned</span>
          </h3>
          <div class="card-tools">
            <button type="button" class="btn-submit mr-1" onclick="selectAll(true)">
              <i class="fas fa-check-double mr-1"></i>Select All
            </button>
            <button type="button" class="btn-cancel" onclick="selectAll(false)">
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
                      $map = ['view' => null, 'create' => null, 'edit' => null, 'delete' => null];
                      foreach ($perms as $p) {
                          $name = strtolower($p->name);
                          if (str_ends_with($name, '-view') || str_contains($name, 'view')) $map['view'] = $p->id;
                          if (str_ends_with($name, '-create') || str_contains($name, 'create')) $map['create'] = $p->id;
                          if (str_ends_with($name, '-edit') || str_contains($name, 'edit')) $map['edit'] = $p->id;
                          if (str_ends_with($name, '-delete') || str_contains($name, 'delete')) $map['delete'] = $p->id;
                      }
                      $assignedCount = $perms->whereIn('id', $assignedIds)->count();
                      $btnText = $assignedCount === $perms->count() ? 'None' : 'All';
                      $moduleKey = md5($module);
                    @endphp
                    <tr>
                      <td class="align-middle">{{ $module }}</td>
                      <td class="text-center align-middle">
                        @if($map['view'])
                          <input class="perm-chk module-{{ $moduleKey }}" type="checkbox" name="permissions[]" value="{{ $map['view'] }}" id="perm_{{ $map['view'] }}" {{ in_array($map['view'], old('permissions', $assignedIds)) ? 'checked' : '' }}>
                        @endif
                      </td>
                      <td class="text-center align-middle">
                        @if($map['create'])
                          <input class="perm-chk module-{{ $moduleKey }}" type="checkbox" name="permissions[]" value="{{ $map['create'] }}" id="perm_{{ $map['create'] }}" {{ in_array($map['create'], old('permissions', $assignedIds)) ? 'checked' : '' }}>
                        @endif
                      </td>
                      <td class="text-center align-middle">
                        @if($map['edit'])
                          <input class="perm-chk module-{{ $moduleKey }}" type="checkbox" name="permissions[]" value="{{ $map['edit'] }}" id="perm_{{ $map['edit'] }}" {{ in_array($map['edit'], old('permissions', $assignedIds)) ? 'checked' : '' }}>
                        @endif
                      </td>
                      <td class="text-center align-middle">
                        @if($map['delete'])
                          <input class="perm-chk module-{{ $moduleKey }}" type="checkbox" name="permissions[]" value="{{ $map['delete'] }}" id="perm_{{ $map['delete'] }}" {{ in_array($map['delete'], old('permissions', $assignedIds)) ? 'checked' : '' }}>
                        @endif
                      </td>
                      <td class="text-center align-middle">
                        <button type="button" class="btn btn-xs btn-outline-primary row-toggle-btn" data-module="{{ $moduleKey }}">{{ $btnText }}</button>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="text-center text-muted py-4">
              <i class="fas fa-key fa-2x mb-2 d-block"></i>No permissions found.
            </div>
          @endif
        </div>
        <div class="card-footer">
          <button type="submit" class="btn-submit">
            <i class="fas fa-save mr-1"></i>Save Changes
          </button>
          <a href="{{ route('roles.index') }}" class="btn-cancel ml-2">
            <i class="fas fa-times mr-1"></i>Cancel
          </a>
        </div>
      </div>
    </form>
  </div>
</section>

<script>
document.addEventListener('click', function (e) {
  // row toggle button
  if (e.target && e.target.classList.contains('row-toggle-btn')) {
    var moduleKey = e.target.getAttribute('data-module');
    var checks = document.querySelectorAll('.module-' + moduleKey);
    var allChecked = Array.from(checks).length > 0 && Array.from(checks).every(function (c) { return c.checked; });
    checks.forEach(function (c) { c.checked = !allChecked; });
    e.target.textContent = allChecked ? 'All' : 'None';
    updateAssignedBadge();
  }
});

function selectAll(state) {
  document.querySelectorAll('.perm-chk').forEach(function(c) { c.checked = state; });
  document.querySelectorAll('.row-toggle-btn').forEach(function(b) { b.textContent = state ? 'None' : 'All'; });
  updateAssignedBadge();
}

function updateAssignedBadge() {
  const count = document.querySelectorAll('.perm-chk:checked').length;
  const badge = document.getElementById('assignedBadge');
  if (badge) badge.textContent = count + ' assigned';
}

document.addEventListener('change', function (e) {
  if (e.target && e.target.classList.contains('perm-chk')) {
    updateAssignedBadge();
    // update related row button text
    var classes = Array.from(e.target.classList).filter(function (cl) { return cl.startsWith('module-'); });
    if (classes.length) {
      var moduleKey = classes[0].replace('module-', '');
      var checks = document.querySelectorAll('.module-' + moduleKey);
      var allChecked = Array.from(checks).length > 0 && Array.from(checks).every(function (c) { return c.checked; });
      var btn = document.querySelector('.row-toggle-btn[data-module="' + moduleKey + '"]');
      if (btn) btn.textContent = allChecked ? 'None' : 'All';
    }
  }
});
</script>
@endsection
