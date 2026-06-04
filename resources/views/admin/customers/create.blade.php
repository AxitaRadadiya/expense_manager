@extends('admin.layouts.app')
@section('title', 'Add Customer')

@section('content')
<div class="content-header">
    <div class="container-fluid-85">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="mr-2 text-teal"></i>Add Customer</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('customer.index') }}">Customers</a></li>
                    <li class="breadcrumb-item active">Create</li>  
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid-85">
    <form id="customer-create-form" class="prevent-multiple-submit" action="{{ route('customer.store') }}" method="POST">
        @csrf
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user-edit mr-2"></i>New Customer</h3>
                <div class="card-tools">
                    <a href="{{ route('customer.index') }}" class="btn-cancel"><i class="fas fa-arrow-left mr-1"></i>Back</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                <div class="form-group col-md-4">
                    <label>Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    @error('name')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>

                <div class="form-group col-md-4">
                    <label>Mobile <span class="text-danger">*</span></label>
                    <input type="text" id="mobile" name="mobile" class="form-control" value="{{ old('mobile') }}" required>
                    @error('mobile')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>

                <div class="form-group col-md-4">
                    <label>Email <span class="text-danger">*</span></label>
                    <input type="text" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    @error('email')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>

                <div class="form-group col-md-4">
                        <label>Website</label>
                        <input type="text" name="website" class="form-control" value="{{ old('website') }}">
                        @error('website')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>
                
                <div class="form-group col-md-4">
                    <label>PAN Number</label>
                    <input type="text" name="pan_number" class="form-control" value="{{ old('pan_number') }}">
                    @error('pan_number')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>
                <div class="form-group col-md-4">
                    <label>GST Number</label>
                    <input type="text" name="gst_number" class="form-control" value="{{ old('gst_number') }}">
                    @error('gst_number')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>
                </div>
            </div>
        </div>
        <div class="card card-outline card-primary shadow-sm mt-4">
            <div class="card-body">
                <ul class="nav nav-tabs" id="customerAddressTabs" role="tablist">
                    <li class="nav-item mr-2">
                        <a class="nav-link active" id="address-tab" data-toggle="tab" href="#address-pane" role="tab" aria-controls="address-pane" aria-selected="true">Address</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="bank-tab" data-toggle="tab" href="#bank-pane" role="tab" aria-controls="bank-pane" aria-selected="false">Bank Details</a>
                    </li>
                </ul>
                <div class="tab-content pt-3" id="customerAddressTabsContent">
                    <div class="tab-pane fade show active" id="address-pane" role="tabpanel" aria-labelledby="address-tab">
                        <div class="row">
                            <div class="card card-outline card-primary p-3 col-md-5 mr-5 ml-5">
                                <p class="text-uppercase text-muted small">Billing Address</p>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Attention</label>
                                        <input type="text" id="billing_attention" name="billing_attention" class="form-control" value="{{ old('billing_attention') }}">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Street</label>
                                        <input type="text" id="billing_street" name="billing_street" class="form-control" value="{{ old('billing_street') }}">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>City</label>
                                        <select id="billing_city" name="billing_city" class="form-control">
                                            <option value="">Select City</option>
                                            <option value="Ahmedabad" {{ old('billing_city') === 'Ahmedabad' ? 'selected' : '' }}>Ahmedabad</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>State</label>
                                        <select id="billing_state" name="billing_state" class="form-control">
                                            <option value="">Select State</option>
                                            <option value="Gujarat" {{ old('billing_state') === 'Gujarat' ? 'selected' : '' }}>Gujarat</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Country</label>
                                        <select id="billing_country" name="billing_country" class="form-control">
                                            <option value="India" {{ old('billing_country') === 'India' ? 'selected' : '' }}>India</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Pin Code</label>
                                        <input type="text" id="billing_pin_code" name="billing_pin_code" class="form-control" value="{{ old('billing_pin_code') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="card card-outline card-primary p-3 col-md-5">
                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="text-uppercase text-muted small">Shipping Address</p>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="same_as" name="same_as" value="1" {{ old('same_as') ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="same_as">Copy Billing Address</label>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Attention</label>
                                        <input type="text" id="shipping_attention" name="shipping_attention" class="form-control" value="{{ old('shipping_attention') }}">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Street</label>
                                        <input type="text" id="shipping_street" name="shipping_street" class="form-control" value="{{ old('shipping_street') }}">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>City</label>
                                        <select id="shipping_city" name="shipping_city" class="form-control">
                                            <option value="">Select City</option>
                                            <option value="Ahmedabad" {{ old('shipping_city') === 'Ahmedabad' ? 'selected' : '' }}>Ahmedabad</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>State</label>
                                        <select id="shipping_state" name="shipping_state" class="form-control">
                                            <option value="">Select State</option>
                                            <option value="Gujarat" {{ old('shipping_state') === 'Gujarat' ? 'selected' : '' }}>Gujarat</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Country</label>
                                        <select id="shipping_country" name="shipping_country" class="form-control">
                                            <option value="India" {{ old('shipping_country') === 'India' ? 'selected' : '' }}>India</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Pin Code</label>
                                        <input type="text" id="shipping_pin_code" name="shipping_pin_code" class="form-control" value="{{ old('shipping_pin_code') }}">
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
                                <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name') }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label>IFSC Code</label>
                                <input type="text" name="ifsc_code" class="form-control" value="{{ old('ifsc_code') }}">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Branch Name</label>
                                <input type="text" name="branch_name" class="form-control" value="{{ old('branch_name') }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Account Number</label>
                                <input type="text" name="account_no" class="form-control" value="{{ old('account_no') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn-submit saveBtn"><i class="fas fa-user-check mr-1"></i>Save Customer</button>
                <a href="{{ route('customer.index') }}" class="btn-cancel ml-2"><i class="fas fa-times mr-1"></i>Cancel</a>
            </div>
        </div>
    </form>
</div>

<script>
(function () {

    var form = document.getElementById('customer-create-form');
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

        if (sameAsCheckbox.checked) {
            copyBilling();
        }

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