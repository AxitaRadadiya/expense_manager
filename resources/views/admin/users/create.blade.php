
@extends('admin.layouts.app')
@section('title', 'Create User')
@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><i class="fas fa-user-plus mr-2 text-primary"></i>Create User</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
          <li class="breadcrumb-item active">Create</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="card card-outline card-primary shadow-sm">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-user-edit mr-2"></i>User Details</h3>
        <div class="card-tools">
          <a href="{{ route('users.index') }}" class="btn btn-default btn-sm">
            <i class="fas fa-arrow-left mr-1"></i>Back
          </a>
        </div>
      </div>
      <div class="card-body">

        <form id="user-create-form" action="{{ route('users.store') }}" method="POST" autocomplete="off" novalidate>
          @csrf

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
                       value="{{ old('name') }}" placeholder="e.g. John Doe" required
                       pattern="[A-Za-z ]+" title="Name can contain only letters and spaces.">
                @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="font-weight-bold">Email Address <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input id="email" name="email" type="email"
                         class="form-control @error('email') is-invalid @enderror"
                         value="{{ old('email') }}" placeholder="user@example.com" required>
                </div>
                @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label class="font-weight-bold">Mobile Number</label>
                <div class="input-group">
                  <input id="mobile" name="mobile" type="text"
                         class="form-control @error('mobile') is-invalid @enderror"
                         value="{{ old('mobile') }}" placeholder="9876543210" maxlength="10"
                         inputmode="numeric" pattern="\d{10}" title="Mobile number must contain exactly 10 digits.">
                </div>
                @error('mobile')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label class="font-weight-bold">Role <span class="text-danger">*</span></label>
                <select id="role_id" name="role_id"
                        class="form-control @error('role_id') is-invalid @enderror" required>
                  <option value="">— Select Role —</option>
                  @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
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
                  <option value="1" {{ old('status','1') == '1' ? 'selected' : '' }}>Active</option>
                  <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')<span class="invalid-feedback">{{ $message }}</span>@enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="font-weight-bold">Opening Balance</label>
                <div class="input-group">
                  <input id="amount" name="amount" type="number" min="0" step="0.01"
                         class="form-control @error('amount') is-invalid @enderror"
                         value="{{ old('amount') }}" placeholder="0.00">
                </div>
                @error('amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                <small class="text-muted d-block mt-1">Assign this user to projects from the project screen after creation.</small>
              </div>
            </div>
          </div>

          <hr>

          {{-- Password --}}
          <p class="text-uppercase text-muted font-weight-bold mb-3" style="font-size:.7rem;letter-spacing:1.4px;">
            <i class="fas fa-lock mr-1"></i> Password
          </p>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="font-weight-bold">Password <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input id="password" name="password" type="password"
                         class="form-control @error('password') is-invalid @enderror"
                         placeholder="Min. 8 characters" required>
                  <div class="input-group-append">
                    <button type="button" class="btn btn-outline-secondary"
                            onclick="togglePw('password','eye1')">
                      <i id="eye1" class="fas fa-eye"></i>
                    </button>
                  </div>
                </div>
                @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="font-weight-bold">Confirm Password <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input id="password_confirmation" name="password_confirmation" type="password"
                         class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Re-enter password" required>
                  <div class="input-group-append">
                    <button type="button" class="btn btn-outline-secondary"
                            onclick="togglePw('password_confirmation','eye2')">
                      <i id="eye2" class="fas fa-eye"></i>
                    </button>
                  </div>
                </div>
                @error('password_confirmation')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>

          <hr>

          {{-- Additional Info --}}
          <p class="text-uppercase text-muted font-weight-bold mb-3" style="font-size:.7rem;letter-spacing:1.4px;">
            <i class="fas fa-sticky-note mr-1"></i> Additional Info
          </p>

          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label class="font-weight-bold">Note</label>
                <textarea id="note" name="note"
                          class="form-control @error('note') is-invalid @enderror"
                          rows="3" placeholder="Optional note about this user…">{{ old('note') }}</textarea>
                @error('note')<span class="invalid-feedback">{{ $message }}</span>@enderror
              </div>
            </div>
          </div>

      </div>
      <div class="card-footer">
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-user-check mr-1"></i>Create User
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

(function () {
  var form = document.getElementById('user-create-form');
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
    var formGroup = input.closest('.form-group');
    if (!formGroup) {
      return null;
    }

    var feedback = formGroup.querySelector('[data-error-for="' + input.id + '"]');
    if (feedback) {
      return feedback;
    }

    feedback = document.createElement('div');
    feedback.className = 'invalid-feedback d-block';
    feedback.setAttribute('data-error-for', input.id);

    var inputGroup = input.closest('.input-group');
    if (inputGroup) {
      inputGroup.insertAdjacentElement('afterend', feedback);
    } else {
      input.insertAdjacentElement('afterend', feedback);
    }

    return feedback;
  }

  function setError(input, message) {
    input.classList.add('is-invalid');
    var feedback = getFeedbackElement(input);
    if (feedback) {
      feedback.textContent = message;
      feedback.style.display = 'block';
    }
  }

  function clearError(input) {
    input.classList.remove('is-invalid');
    var feedback = getFeedbackElement(input);
    if (feedback) {
      feedback.textContent = '';
      feedback.style.display = 'none';
    }
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
      setError(passwordInput, 'Password is required.');
      return false;
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
    validatePassword();
    if (confirmPasswordInput.value) {
      validatePasswordConfirmation();
    }
  });
  confirmPasswordInput.addEventListener('input', validatePasswordConfirmation);

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
    }
  });
})();
</script>
@endsection
