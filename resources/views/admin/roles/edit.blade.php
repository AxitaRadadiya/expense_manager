{{-- resources/views/admin/roles/edit.blade.php --}}
@extends('admin.layouts.app')
@section('title', 'Edit Role')
@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">
          <i class="fas fa-pen mr-2 text-warning"></i>Edit Role
          <small class="text-muted" style="font-size:.75rem;">— {{ $role->name }}</small>
        </h1>
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
  <div class="container-fluid">
    <form action="{{ route('roles.update', $role->id) }}" method="POST">
      @csrf
      @method('PUT')

      {{-- Role Name --}}
      <div class="card card-outline card-warning shadow-sm">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-tag mr-2"></i>Role Info</h3>
          <div class="card-tools">
            <span class="badge badge-warning mr-2">
              <i class="fas fa-shield-alt mr-1"></i>{{ $role->name }}
            </span>
            <a href="{{ route('roles.index') }}" class="btn btn-default btn-sm">
              <i class="fas fa-arrow-left mr-1"></i>Back
            </a>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="name" class="font-weight-bold">
                  Role Name <span class="text-danger">*</span>
                </label>
                <input id="name" name="name" type="text"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $role->name) }}" required>
                @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Permissions --}}
      <div class="card card-outline card-warning shadow-sm">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fas fa-key mr-2"></i>Permissions
            <span class="badge badge-secondary ml-1">{{ $permissions->flatten()->count() }} total</span>
            <span class="badge badge-success ml-1" id="assignedBadge">{{ count($assignedIds) }} assigned</span>
          </h3>
          <div class="card-tools">
            <button type="button" class="btn btn-warning btn-sm mr-1" onclick="selectAll(true)">
              <i class="fas fa-check-double mr-1"></i>Select All
            </button>
            <button type="button" class="btn btn-default btn-sm" onclick="selectAll(false)">
              <i class="fas fa-times mr-1"></i>Clear All
            </button>
          </div>
        </div>
        <div class="card-body">
          @if($permissions->isNotEmpty())
            <div class="row">
              @foreach($permissions as $group => $perms)
                @php $groupAssigned = $perms->whereIn('id', $assignedIds)->count(); @endphp
                <div class="col-md-4 col-sm-6 mb-4">
                  <div class="card card-outline card-primary mb-0 shadow-none">
                    <div class="card-header p-2">
                      <h6 class="card-title mb-0 d-flex align-items-center justify-content-between">
                        <span>
                          <i class="fas fa-layer-group mr-1 text-primary"></i>
                          {{ $group }}
                          <span class="badge badge-light ml-1">{{ $perms->count() }}</span>
                          @if($groupAssigned > 0)
                            <span class="badge badge-success ml-1">{{ $groupAssigned }}✓</span>
                          @endif
                        </span>
                        <button type="button" class="btn btn-xs btn-outline-warning group-toggle-btn"
                                onclick="toggleGroup(this)">
                          {{ $groupAssigned === $perms->count() ? 'None' : 'All' }}
                        </button>
                      </h6>
                    </div>
                    <div class="card-body p-2">
                      @foreach($perms as $perm)
                        <div class="icheck-primary mb-1">
                          <input class="perm-chk" type="checkbox"
                                 name="permissions[]" value="{{ $perm->id }}"
                                 id="perm_{{ $perm->id }}"
                                 {{ in_array($perm->id, old('permissions', $assignedIds)) ? 'checked' : '' }}>
                          <label for="perm_{{ $perm->id }}" style="font-size:.85rem;">
                            {{ $perm->name }}
                          </label>
                        </div>
                      @endforeach
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <div class="text-center text-muted py-4">
              <i class="fas fa-key fa-2x mb-2 d-block"></i>No permissions found.
            </div>
          @endif
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-warning">
            <i class="fas fa-save mr-1"></i>Save Changes
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
  updateAssignedBadge();
}
function selectAll(state) {
  document.querySelectorAll('.perm-chk').forEach(c => { c.checked = state; });
  document.querySelectorAll('.group-toggle-btn').forEach(b => { b.textContent = state ? 'None' : 'All'; });
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
    const cardBody = e.target.closest('.card-body');
    const checkboxes = cardBody.querySelectorAll('.perm-chk');
    const allChecked = [...checkboxes].every(c => c.checked);
    const toggleBtn = e.target.closest('.card').querySelector('.group-toggle-btn');
    if (toggleBtn) toggleBtn.textContent = allChecked ? 'None' : 'All';
  }
});
</script>
@endsection