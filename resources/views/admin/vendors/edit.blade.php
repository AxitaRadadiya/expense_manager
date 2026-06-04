@extends('admin.layouts.app')
@section('title', 'Edit Vendor')

@section('content')
<div class="content-header">
    <div class="container-fluid-85">
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

<div class="container-fluid-85">
    <form id="vendor-edit-form" action="{{ route('vendor.update', $vendor->id) }}" method="POST">
        @csrf
        @method('PATCH')
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-pen mr-2"></i>Edit Vendor</h3>
                <div class="card-tools">
                    <a href="{{ route('vendor.index') }}" class="btn-cancel"><i class="fas fa-arrow-left mr-1"></i>Back</a>
                </div>
            </div>
        
            <div class="card-body">
                <div class="row">
                <div class="form-group col-md-4">
                    <label>First Name <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $vendor->first_name) }}" required>
                    @error('first_name')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>
                <div class="form-group col-md-4">
                    <label>Last Name <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $vendor->last_name) }}" required>
                    @error('last_name')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>

                <div class="form-group col-md-4">
                    <label>Company Name <span class="text-danger">*</span></label>
                    <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $vendor->company_name) }}" required>
                    @error('company_name')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>

                <div class="form-group col-md-4">
                    <label>Mobile <span class="text-danger">*</span></label>
                    <input type="text" id="mobile" name="mobile" class="form-control" value="{{ old('mobile', $vendor->mobile) }}" required>
                    @error('mobile')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>

                <div class="form-group col-md-4">
                    <label>Email <span class="text-danger">*</span></label>
                    <input type="text" id="email" name="email" class="form-control" value="{{ old('email', $vendor->email) }}" required>
                    @error('email')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>

                <div class="form-group col-md-4">
                        <label>Website</label>
                        <input type="text" name="website" class="form-control" value="{{ old('website', $vendor->website) }}">
                        @error('website')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>
                </div>
            </div>
        </div>

        <div class="card card-outline card-primary shadow-sm mt-4">
            <div class="card-body">        
                @php($addr = $vendor->relationLoaded('address') ? $vendor->address : $vendor->address()->first())
                @php($bank = $vendor->relationLoaded('bankDetail') ? $vendor->bankDetail : $vendor->bankDetail()->first())
                <ul class="nav nav-tabs" id="vendorAddressTabs" role="tablist">
                    <li class="nav-item mr-2">
                        <a class="nav-link active" id="address-tab" data-toggle="tab" href="#address-pane" role="tab" aria-controls="address-pane" aria-selected="true">Address</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="bank-tab" data-toggle="tab" href="#bank-pane" role="tab" aria-controls="bank-pane" aria-selected="false">Bank Details</a>
                    </li>
                </ul>
                <div class="tab-content pt-3" id="vendorAddressTabsContent">
                    <div class="tab-pane fade show active" id="address-pane" role="tabpanel" aria-labelledby="address-tab">
                        <div class="row">
                            <div class="card card-outline card-primary p-3 col-md-5 mr-5 ml-5">
                                <p class="text-uppercase text-muted small">Billing Address</p>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Attention</label>
                                        <input type="text" id="billing_attention" name="billing_attention" class="form-control" value="{{ old('billing_attention', optional($addr)->billing_attention) }}">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Street</label>
                                        <input type="text" id="billing_street" name="billing_street" class="form-control" value="{{ old('billing_street', optional($addr)->billing_street) }}">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>City</label>
                                        <select id="billing_city" name="billing_city" class="form-control">
                                            <option value="">Select City</option>
                                            <option value="Ahmedabad" {{ (old('billing_city', optional($addr)->billing_city) === 'Ahmedabad') ? 'selected' : '' }}>Ahmedabad</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>State</label>
                                        <select id="billing_state" name="billing_state" class="form-control">
                                            <option value="">Select State</option>
                                            <option value="Gujarat" {{ (old('billing_state', optional($addr)->billing_state) === 'Gujarat') ? 'selected' : '' }}>Gujarat</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Country</label>
                                        <select id="billing_country" name="billing_country" class="form-control">
                                            <option value="India" {{ (old('billing_country', optional($addr)->billing_country) === 'India') ? 'selected' : '' }}>India</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Pincode</label>
                                        <input type="text" id="billing_pin_code" name="billing_pin_code" class="form-control" value="{{ old('billing_pin_code', optional($addr)->billing_pin_code) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="card card-outline card-primary p-3 col-md-5">
                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="text-uppercase text-muted small">Shipping Address</p>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="same_as" name="same_as" value="1" {{ old('same_as', optional($addr)->same_as) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="same_as">Copy Billing Address</label>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Attention</label>
                                        <input type="text" id="shipping_attention" name="shipping_attention" class="form-control" value="{{ old('shipping_attention', optional($addr)->shipping_attention) }}">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Street</label>
                                        <input type="text" id="shipping_street" name="shipping_street" class="form-control" value="{{ old('shipping_street', optional($addr)->shipping_street) }}">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>City</label>
                                        <select id="shipping_city" name="shipping_city" class="form-control">
                                            <option value="">Select City</option>
                                            <option value="Ahmedabad" {{ (old('shipping_city', optional($addr)->shipping_city) === 'Ahmedabad') ? 'selected' : '' }}>Ahmedabad</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>State</label>
                                        <select id="shipping_state" name="shipping_state" class="form-control">
                                            <option value="">Select State</option>
                                            <option value="Gujarat" {{ (old('shipping_state', optional($addr)->shipping_state) === 'Gujarat') ? 'selected' : '' }}>Gujarat</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Country</label>
                                        <select id="shipping_country" name="shipping_country" class="form-control">
                                            <option value="India" {{ (old('shipping_country', optional($addr)->shipping_country) === 'India') ? 'selected' : '' }}>India</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Pincode</label>
                                        <input type="text" id="shipping_pin_code" name="shipping_pin_code" class="form-control" value="{{ old('shipping_pin_code', optional($addr)->shipping_pin_code) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="bank-pane" role="tabpanel" aria-labelledby="bank-tab">
                        <p class="text-uppercase text-muted small">Bank Details</p>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Bank Name</label>
                                <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', optional($bank)->bank_name) }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label>IFSC Code</label>
                                <input type="text" name="ifsc_code" class="form-control" value="{{ old('ifsc_code', optional($bank)->ifsc_code) }}">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Branch Name</label>
                                <input type="text" name="branch_name" class="form-control" value="{{ old('branch_name', optional($bank)->branch_name) }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Account Number</label>
                                <input type="text" name="account_no" class="form-control" value="{{ old('account_no', optional($bank)->account_no) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn-submit"><i class="fas fa-save mr-1"></i>Update Vendor</button>
                <a href="{{ route('vendor.index') }}" class="btn-cancel ml-2"><i class="fas fa-times mr-1"></i>Cancel</a>
            </div>
        </div>
    </form>
</div>

<script>
(function () {

    var form = document.getElementById('vendor-edit-form');
    if (!form) return;

    var mobileInput = document.getElementById('mobile');
    var emailInput = document.getElementById('email');

    var mobilePattern = /^\d{10}$/;
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    function validateMobile() {
        var value = mobileInput.value.trim();

        if (!value) {
            toastr.error(
                'Mobile number is required.',
                'Validation Error'
            );
            mobileInput.focus();
            return false;
        }

        if (!mobilePattern.test(value)) {
            toastr.error(
                'Mobile number must be exactly 10 digits.',
                'Validation Error'
            );
            mobileInput.focus();
            return false;
        }
        return true;
    }
    function validateEmail() {
        var value = emailInput.value.trim();
        if (!value) {
            toastr.error(
                'Email is required.',
                'Validation Error'
            );
            emailInput.focus();
            return false;
        }
        if (!emailPattern.test(value)) {
            toastr.error(
                'Enter a valid email address.',
                'Validation Error'
            );
            emailInput.focus();
            return false;
        }

        return true;
    }
    mobileInput.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 10);
    });
    form.addEventListener('submit', function (event) {

        toastr.clear();

        var isMobileValid = validateMobile();
        var isEmailValid = validateEmail();

        if (!isMobileValid || !isEmailValid) {
            event.preventDefault();
        }
    });

    // Copy Billing -> Shipping when checkbox is checked
    var sameAsCheckbox = document.getElementById('same_as');
    var billingFields = ['attention','street','city','state','pin_code','country'];
    function getFieldId(prefix, name) { return prefix + '_' + name; }
    function copyBilling() {
        billingFields.forEach(function(name){
            var b = document.getElementById(getFieldId('billing', name));
            var s = document.getElementById(getFieldId('shipping', name));
            if (!b || !s) return;
            s.value = b.value;
            s.disabled = true;
        });
    }
    function enableShipping() {
        billingFields.forEach(function(name){
            var s = document.getElementById(getFieldId('shipping', name));
            if (!s) return;
            s.disabled = false;
        });
    }
    if (sameAsCheckbox) {
        sameAsCheckbox.addEventListener('change', function(){
            if (this.checked) {
                copyBilling();
            } else {
                enableShipping();
            }
        });

        // If checked on load, copy values
        if (sameAsCheckbox.checked) {
            copyBilling();
        }

        // When billing inputs change and checkbox is checked, update shipping
        billingFields.forEach(function(name){
            var b = document.getElementById(getFieldId('billing', name));
            if (!b) return;
            b.addEventListener('input', function(){
                if (sameAsCheckbox.checked) {
                    var s = document.getElementById(getFieldId('shipping', name));
                    if (s) s.value = this.value;
                }
            });
        });
    }

})();
</script>
@endsection