@extends('admin.layouts.app')
@section('title', 'Create Role')

@section('content')

{{-- Page Header --}}
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">
          <i class="fas fa-plus-circle mr-2 text-warning"></i>Create Role
        </h1>
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

{{-- Content --}}
<section class="content">
  <div class="container-fluid">

    <form action="{{ route('roles.store') }}" method="POST">
      @csrf

      {{-- ── Role Name ── --}}
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fas fa-tag mr-2"></i>Role Info
          </h3>
          <div class="card-tools">
            <a href="{{ route('roles.index') }}" class="btn btn-default btn-sm">
              <i class="fas fa-arrow-left mr-1"></i>Back
            </a>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="name">
                  Role Name <span class="text-danger">*</span>
                </label>
                <input
                  id="name"
                  name="name"
                  type="text"
                  class="form-control @error('name') is-invalid @enderror"
                  value="{{ old('name') }}"
                  placeholder="e.g. Manager, Editor, Viewer"
                  required
                >
                @error('name')
                  <span class="invalid-feedback">{{ $message }}</span>
                @enderror
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- ── Permissions ── --}}
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fas fa-key mr-2"></i>Permissions
            <span class="badge badge-secondary ml-2">
              {{ $permissions->flatten()->count() }} total
            </span>
          </h3>
          <div class="card-tools">
            <button type="button" class="btn btn-warning btn-sm" onclick="selectAll(true)">
              <i class="fas fa-check-double mr-1"></i>Select All
            </button>
            <button type="button" class="btn btn-default btn-sm ml-1" onclick="selectAll(false)">
              <i class="fas fa-times mr-1"></i>Clear All
            </button>
          </div>
        </div>
        <div class="card-body">

          @if($permissions->isNotEmpty())
            <div class="row">
              @foreach($permissions as $group => $perms)
              <div class="col-md-4 col-sm-6 mb-4">

                {{-- Group Card --}}
                <div class="card card-outline card-primary mb-0">
                  <div class="card-header p-2">
                    <h6 class="card-title mb-0 d-flex align-items-center justify-content-between">
                      <span>
                        <i class="fas fa-layer-group mr-1 text-primary"></i>
                        {{ $group }}
                        <span class="badge badge-light ml-1">{{ $perms->count() }}</span>
                      </span>
                      <button type="button"
                              class="btn btn-xs btn-outline-warning group-toggle-btn"
                              onclick="toggleGroup(this)">
                        All
                      </button>
                    </h6>
                  </div>
                  <div class="card-body p-2">
                    @foreach($perms as $perm)
                    <div class="form-check mb-1">
                      <input
                        class="form-check-input perm-chk"
                        type="checkbox"
                        name="permissions[]"
                        value="{{ $perm->id }}"
                        id="perm_{{ $perm->id }}"
                        {{ in_array($perm->id, old('permissions', [])) ? 'checked' : '' }}
                      >
                      <label class="form-check-label" for="perm_{{ $perm->id }}" style="font-size:.85rem;">
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
// Toggle all checkboxes in one group
function toggleGroup(btn) {
  const cardBody   = btn.closest('.card').querySelector('.card-body');
  const checkboxes = cardBody.querySelectorAll('.perm-chk');
  const allChecked = [...checkboxes].every(c => c.checked);
  checkboxes.forEach(c => { c.checked = !allChecked; });
  btn.textContent  = allChecked ? 'All' : 'None';
}

// Select / deselect every permission on the page
function selectAll(state) {
  document.querySelectorAll('.perm-chk').forEach(c => { c.checked = state; });
  document.querySelectorAll('.group-toggle-btn').forEach(b => {
    b.textContent = state ? 'None' : 'All';
  });
}
</script>

@endsection