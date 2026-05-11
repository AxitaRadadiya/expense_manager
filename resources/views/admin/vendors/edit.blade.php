@extends('admin.layouts.app')
@section('title', 'Edit Vendor')

@section('content')
<div class="content-header">
    <div class="container-fluid-80">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="mr-2 text-teal"></i>Edit Vendor</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('vendor.index') }}">Vendors</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid-80">
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-pen mr-2"></i>Edit Vendor</h3>
            <div class="card-tools">
                <a href="{{ route('vendor.index') }}" class="btn-cancel"><i class="fas fa-arrow-left mr-1"></i>Back</a>
            </div>
        </div>
        <form id="vendor-edit-form" action="{{ route('vendor.update', $vendor->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="card-body">
                <div class="form-group">
                    <label>Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $vendor->name) }}" required>
                    @error('name')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label>Company Name <span class="text-danger">*</span></label>
                    <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $vendor->company_name) }}" required>
                    @error('company_name')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label>Mobile <span class="text-danger">*</span></label>
                    <input type="text" id="mobile" name="mobile" class="form-control" value="{{ old('mobile', $vendor->mobile) }}" required>
                    @error('mobile')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label>Email <span class="text-danger">*</span></label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $vendor->email) }}" required>
                    @error('email')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label>Address <span class="text-danger">*</span></label>
                    <textarea name="address" class="form-control" rows="3" required>{{ old('address', $vendor->address) }}</textarea>
                    @error('address')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn-submit"><i class="fas fa-save mr-1"></i>Update Vendor</button>
                <a href="{{ route('vendor.index') }}" class="btn-cancel ml-2"><i class="fas fa-times mr-1"></i>Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    (function () {

    var form = document.getElementById('vendor-edit-form');
    if (!form) return;

    var mobileInput = document.getElementById('mobile');
    var emailInput = document.getElementById('email');

    var mobilePattern = /^\d{10}$/;
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    function getFeedbackElement(input) {
        var formGroup = input.closest('.form-group');
        var feedback = formGroup.querySelector(
            '[data-error-for="' + input.id + '"]'
        );

        if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback d-block';
            feedback.setAttribute('data-error-for', input.id);
            input.insertAdjacentElement('afterend', feedback);
        }
        return feedback;
    }

    function setError(input, message) {
        input.classList.add('is-invalid');
        var feedback = getFeedbackElement(input);
        feedback.textContent = message;
    }

    function clearError(input) {
        input.classList.remove('is-invalid');
        var feedback = getFeedbackElement(input);
        feedback.textContent = '';
    }

    // MOBILE VALIDATION
    function validateMobile() {
        var value = mobileInput.value.trim();
        if (!value) {
            setError(mobileInput, 'Mobile number is required.');
            return false;
        }
        if (!mobilePattern.test(value)) {
            setError(
                mobileInput,
                'Mobile number must be exactly 10 digits.'
            );
            return false;
        }
        clearError(mobileInput);
        return true;
    }

    // EMAIL VALIDATION
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

    // ONLY NUMBERS IN MOBILE
    mobileInput.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 10);
        validateMobile();
    });

    // EMAIL VALIDATION
    emailInput.addEventListener('input', validateEmail);

    // FORM SUBMIT
    form.addEventListener('submit', function (event) {
        var isValid = validateMobile() && validateEmail();
        if (!isValid) {
            event.preventDefault();
        }
    });
})();
</script>

@endsection