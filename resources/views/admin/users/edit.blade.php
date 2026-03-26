
@extends('admin.layouts.app')
@section('title', 'Edit User')
@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><i class="fas fa-user-edit mr-2 text-primary"></i>Edit User</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
          <li class="breadcrumb-item active">{{ $user->name }}</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="card card-outline card-primary shadow-sm">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-pen mr-2"></i>Edit User
          <span class="badge badge-primary ml-2">{{ $user->name }}</span>
        </h3>
        <div class="card-tools">
          <a href="{{ route('users.index') }}" class="btn btn-default btn-sm">
            <i class="fas fa-arrow-left mr-1"></i>Back
          </a>
        </div>
      </div>
      <div class="card-body">

        @if($errors->any())
          <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <h5><i class="icon fas fa-ban"></i> Error</h5>
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
          </div>
        @endif

        <form action="{{ route('users.update', $user->id) }}" method="POST" autocomplete="off">
          @csrf
          @method('PUT')

          {{-- Basic Info --}}
          <p class="text-uppercase text-muted font-weight-bold mb-3" style="font-size:.7rem;letter-spacing:1.4px;">
            <i class="fas fa-id-card mr-1"></i> Basic Info
          </p>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="font-weight-bold">Full Name <span class="text-danger">*</span></label>
                <input id="name" name="name" type="text"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $user->name) }}" required>
                @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="font-weight-bold">Email Address <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input id="email" name="email" type="email"
                         class="form-control @error('email') is-invalid @enderror"
                         value="{{ old('email', $user->email) }}" required>
                  @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label class="font-weight-bold">Mobile Number</label>
                <div class="input-group">
                  <input id="mobile" name="mobile" type="text"
                         class="form-control @error('mobile') is-invalid @enderror"
                         value="{{ old('mobile', $user->mobile) }}" maxlength="15">
                  @error('mobile')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label class="font-weight-bold">Role <span class="text-danger">*</span></label>
                <select id="role_id" name="role_id"
                        class="form-control @error('role_id') is-invalid @enderror" required>
                  <option value="">— Select Role —</option>
                  @foreach($roles as $role)
                    <option value="{{ $role->id }}"
                      {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                      {{ $role->name }}
                    </option>
                  @endforeach
                </select>
                @error('role_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label class="font-weight-bold">Status <span class="text-danger">*</span></label>
                <select id="status" name="status"
                        class="form-control @error('status') is-invalid @enderror" required>
                  <option value="1" {{ old('status', $user->status) == 1 ? 'selected' : '' }}>Active</option>
                  <option value="0" {{ old('status', $user->status) == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')<span class="invalid-feedback">{{ $message }}</span>@enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="font-weight-bold">Projects</label>
                <select id="project_ids" name="project_ids[]"
                        class="form-control select2 @error('project_ids') is-invalid @enderror @error('project_ids.*') is-invalid @enderror"
                        multiple
                        data-placeholder="Select one or more projects">
                  @php($selectedProjectIds = old('project_ids', $user->projects->pluck('id')->all()))
                  @foreach($projects as $project)
                    <option value="{{ $project->id }}"
                      {{ in_array($project->id, $selectedProjectIds) ? 'selected' : '' }}>
                      {{ $project->name }}
                    </option>
                  @endforeach
                </select>
                <small class="text-muted d-block mt-1">You can assign multiple projects. The first selected project stays as the primary fallback.</small>
                @error('project_ids')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                @error('project_ids.*')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="font-weight-bold">Opening Balance</label>
                <div class="input-group">
                  <div class="input-group-prepend"><span class="input-group-text">₹</span></div>
                  <input id="amount" name="amount" type="number" min="0" step="0.01"
                         class="form-control @error('amount') is-invalid @enderror"
                         value="{{ old('amount', $user->amount) }}" placeholder="0.00">
                  @error('amount')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="font-weight-bold">Note</label>
                <textarea id="note" name="note"
                          class="form-control @error('note') is-invalid @enderror"
                          rows="3" placeholder="Optional note…">{{ old('note', $user->note) }}</textarea>
                @error('note')<span class="invalid-feedback">{{ $message }}</span>@enderror
              </div>
            </div>
          </div>

          <hr>

          {{-- Change Password (collapsible) --}}
          <p class="text-uppercase text-muted font-weight-bold mb-0" style="font-size:.7rem;letter-spacing:1.4px;">
            <i class="fas fa-lock mr-1"></i> Change Password
          </p>
          <div class="callout callout-warning mt-2 mb-3" style="cursor:pointer;" onclick="togglePwSection()">
            <p class="mb-0 font-weight-bold" style="font-size:.85rem;">
              <i class="fas fa-key mr-1"></i> Click to change password for this user
              <i id="pw-caret" class="fas fa-chevron-down float-right mt-1" style="font-size:.75rem;transition:transform .2s;"></i>
            </p>
          </div>

          <div id="pw-section" style="display:none;">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="font-weight-bold">New Password</label>
                  <div class="input-group">
                    <input id="password" name="password" type="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Leave blank to keep current">
                    <div class="input-group-append">
                      <button type="button" class="btn btn-outline-secondary"
                              onclick="togglePw('password','eye1')">
                        <i id="eye1" class="fas fa-eye"></i>
                      </button>
                    </div>
                    @error('password')<span class="invalid-feedback">{{ $message }}</span>@enderror
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="font-weight-bold">Confirm Password</label>
                  <div class="input-group">
                    <input id="password_confirmation" name="password_confirmation" type="password"
                           class="form-control" placeholder="Re-enter new password">
                    <div class="input-group-append">
                      <button type="button" class="btn btn-outline-secondary"
                              onclick="togglePw('password_confirmation','eye2')">
                        <i id="eye2" class="fas fa-eye"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

      </div>
      <div class="card-footer">
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save mr-1"></i>Save Changes
        </button>
        <a href="{{ route('users.index') }}" class="btn btn-default ml-2">
          <i class="fas fa-times mr-1"></i>Cancel
        </a>
      </div>

        </form>
    </div>
  </div>
</section>

<script>
function togglePw(inputId, iconId) {
  var inp = document.getElementById(inputId);
  var ico = document.getElementById(iconId);
  if (inp.type === 'password') {
    inp.type = 'text';
    ico.classList.replace('fa-eye', 'fa-eye-slash');
  } else {
    inp.type = 'password';
    ico.classList.replace('fa-eye-slash', 'fa-eye');
  }
}
function togglePwSection() {
  var sec = document.getElementById('pw-section');
  var caret = document.getElementById('pw-caret');
  var visible = sec.style.display !== 'none';
  sec.style.display = visible ? 'none' : 'block';
  caret.style.transform = visible ? 'rotate(0deg)' : 'rotate(180deg)';
}
@error('password') togglePwSection(); @enderror
</script>
@endsection
