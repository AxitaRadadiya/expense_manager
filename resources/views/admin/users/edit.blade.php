
@extends('admin.layouts.app')
@section('title', 'Edit User')
@section('content')

<div class="content-header">
  <div class="container-fluid-80">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><i class="mr-2 text-primary"></i>Edit User</h1>
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
  <div class="container-fluid-80">
    <div class="card card-outline card-primary shadow-sm">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-pen mr-2"></i>Edit User
          <span class="badge badge-primary ml-2">{{ $user->name }}</span>
        </h3>
        <div class="card-tools">
          <a href="{{ route('users.index') }}" class="btn-cancel">
            <i class="fas fa-arrow-left mr-1"></i>Back
          </a>
        </div>
      </div>
      <div class="card-body">

        <form id="user-edit-form" action="{{ route('users.update', $user->id) }}" method="POST" autocomplete="off" novalidate>
          @csrf
          @method('PUT')

          {{-- Basic Info --}}
          <p class="text-uppercase text-muted font-weight-bold mb-3" style="font-size:.7rem;letter-spacing:1.4px;">
            <i class="fas fa-id-card mr-1"></i> Basic Info
          </p>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label class="font-weight-bold">Full Name <span class="text-danger">*</span></label>
                <input id="name" name="name" type="text"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $user->name) }}" required
                       pattern="[A-Za-z ]+" title="Name can contain only letters and spaces.">
                @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
              </div>
            </div>
            <div class="col-md-4">
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
                         value="{{ old('mobile', $user->mobile) }}" maxlength="10"
                         inputmode="numeric" pattern="\d{10}" title="Mobile number must contain exactly 10 digits.">
                  @error('mobile')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label class="font-weight-bold">Opening Balance</label>
                <div class="input-group">
                  <div class="input-group-prepend"><span class="input-group-text">₹</span></div>
                  <input id="amount" name="amount" type="number" min="0" step="0.01"
                         class="form-control @error('amount') is-invalid @enderror"
                         value="{{ old('amount', $user->amount) }}" placeholder="0.00">
                  @error('amount')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <small class="text-muted d-block mt-1">Project assignment is managed from each project screen.</small>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label class="font-weight-bold">Role <span class="text-danger">*</span></label>
                <select id="role_id" name="role_id"
                        class="form-control select2 @error('role_id') is-invalid @enderror" required>
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
                        class="form-control select2 @error('status') is-invalid @enderror" required>
                  <option value="1" {{ old('status', $user->status) == 1 ? 'selected' : '' }}>Active</option>
                  <option value="0" {{ old('status', $user->status) == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')<span class="invalid-feedback">{{ $message }}</span>@enderror
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="font-weight-bold">Assigned Projects</label>
                <input type="text" class="form-control" value="{{ $user->assignedProjectNames() ?: 'No projects assigned' }}" readonly>
                <small class="text-muted d-block mt-1">Open a project to add or remove this user.</small>
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
        <button type="submit" class="btn-submit">
          <i class="fas fa-save mr-1"></i>Save Changes
        </button>
        <a href="{{ route('users.index') }}" class="btn-cancel ml-2">
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
(function () {
  var form = document.getElementById('user-edit-form');
  if (!form) return;

  var nameInput = document.getElementById('name');
  var mobileInput = document.getElementById('mobile');
  var emailInput = document.getElementById('email');
  var passwordInput = document.getElementById('password');
  var confirmPasswordInput = document.getElementById('password_confirmation');
  var amountInput = document.getElementById('amount');
  var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  var namePattern = /^[A-Za-z ]+$/;
  var mobilePattern = /^\d{10}$/;
  var passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/;

  function getFeedbackElement(input) {
    var container = input.closest('.input-group') || input;
    var next = container.nextElementSibling;
    if (next && next.classList.contains('invalid-feedback')) {
      return next;
    }

    var feedback = document.createElement('span');
    feedback.className = 'invalid-feedback';
    container.insertAdjacentElement('afterend', feedback);
    return feedback;
  }

  function setError(input, message) {
    input.classList.add('is-invalid');
    getFeedbackElement(input).textContent = message;
  }

  function clearError(input) {
    input.classList.remove('is-invalid');
    getFeedbackElement(input).textContent = '';
  }

  function validateName() {
    var value = nameInput.value.trim();
    if (!value) {
      setError(nameInput, 'Name is required.');
      return false;
    }
    if (!namePattern.test(value)) {
      setError(nameInput, 'Name can contain only letters and spaces.');
      return false;
    }
    clearError(nameInput);
    return true;
  }

  function validateMobile() {
    var value = mobileInput.value.trim();
    if (!value) {
      clearError(mobileInput);
      return true;
    }
    if (!mobilePattern.test(value)) {
      setError(mobileInput, 'Mobile number must be exactly 10 digits.');
      return false;
    }
    clearError(mobileInput);
    return true;
  }

  function validateEmail() {
    var value = emailInput.value.trim();
    if (!value) {
      setError(emailInput, 'Email is required.');
      return false;
    }
    if (!emailPattern.test(value)) {
      setError(emailInput, 'Enter a valid email address.');
      return false;
    }
    clearError(emailInput);
    return true;
  }

  function validatePassword() {
    var value = passwordInput.value;
    if (!value) {
      clearError(passwordInput);
      return true;
    }
    if (!passwordPattern.test(value)) {
      setError(passwordInput, 'Password must be at least 8 characters and include uppercase, lowercase, a number, and a special character.');
      return false;
    }
    clearError(passwordInput);
    return true;
  }

  function validateAmount() {
    var value = amountInput.value.trim();
    if (!value) {
      clearError(amountInput);
      return true;
    }

    if (isNaN(value) || Number(value) < 0) {
      setError(amountInput, 'Opening amount must be 0 or greater.');
      return false;
    }

    clearError(amountInput);
    return true;
  }

  function validatePasswordConfirmation() {
    if (!passwordInput.value && !confirmPasswordInput.value) {
      clearError(confirmPasswordInput);
      return true;
    }
    if (!confirmPasswordInput.value) {
      setError(confirmPasswordInput, 'Please confirm the password.');
      return false;
    }
    if (passwordInput.value !== confirmPasswordInput.value) {
      setError(confirmPasswordInput, 'Password confirmation does not match.');
      return false;
    }
    clearError(confirmPasswordInput);
    return true;
  }

  nameInput.addEventListener('input', function () {
    this.value = this.value.replace(/[^A-Za-z ]/g, '');
    validateName();
  });

  mobileInput.addEventListener('input', function () {
    this.value = this.value.replace(/\D/g, '').slice(0, 10);
    validateMobile();
  });

  emailInput.addEventListener('input', validateEmail);
  amountInput.addEventListener('input', validateAmount);
  passwordInput.addEventListener('input', function () {
    if (this.value && document.getElementById('pw-section').style.display === 'none') {
      togglePwSection();
    }
    validatePassword();
    if (confirmPasswordInput.value) {
      validatePasswordConfirmation();
    }
  });
  confirmPasswordInput.addEventListener('input', function () {
    if (this.value && document.getElementById('pw-section').style.display === 'none') {
      togglePwSection();
    }
    validatePasswordConfirmation();
  });

  form.addEventListener('submit', function (event) {
    var isValid = [
      validateName(),
      validateMobile(),
      validateEmail(),
      validateAmount(),
      validatePassword(),
      validatePasswordConfirmation()
    ].every(Boolean);

    if (!isValid) {
      event.preventDefault();
      if ((passwordInput.value || confirmPasswordInput.value) && document.getElementById('pw-section').style.display === 'none') {
        togglePwSection();
      }
    }
  });
})();
@error('password') togglePwSection(); @enderror
</script>
@endsection
