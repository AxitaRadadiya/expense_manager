@extends('admin.layouts.app')
@section('title', 'Add Expense')

@section('content')
<div class="content-header">
  <div class="container-fluid-80">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><i class="mr-2 text-primary"></i>Add Expense</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('expense.index') }}">Expenses</a></li>
          <li class="breadcrumb-item active">Create</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid-80">
    <div class="card card-outline card-primary shadow-sm">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-file-invoice mr-2"></i>Expense Details</h3>
        <div class="card-tools">
          <a href="{{ route('expense.index') }}" class="btn-cancel">
            <i class="fas fa-arrow-left mr-1"></i>Back
          </a>
        </div>
      </div>
      <div class="card-body">
        <form action="{{ route('expense.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="status" value="pending">

          <p class="text-uppercase text-muted font-weight-bold mb-3" style="font-size:.7rem;letter-spacing:1.4px;">
            <i class="fas fa-receipt mr-1"></i> Expense Info
          </p>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="projects_id" class="font-weight-bold">Project <span class="text-danger">*</span></label>
                <select class="form-control @error('projects_id') is-invalid @enderror" name="projects_id" id="projects_id" required>
                  <option value="">-- Select Project --</option>
                  @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ old('projects_id') == $project->id ? 'selected' : '' }}>
                      {{ $project->name }}
                    </option>
                  @endforeach
                </select>
                @error('projects_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="expense_date" class="font-weight-bold">Expense Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control @error('expense_date') is-invalid @enderror" name="expense_date" id="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                @error('expense_date')<span class="invalid-feedback">{{ $message }}</span>@enderror
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="category" class="font-weight-bold">Expense Category <span class="text-danger">*</span></label>
                <select class="form-control @error('category') is-invalid @enderror" name="category" id="category" required>
                  <option value="">-- Select Category --</option>
                  @foreach($categories as $cat)
                    <option value="{{ $cat->name }}" {{ old('category') == $cat->name ? 'selected' : '' }}>{{ $cat->name }}</option>
                  @endforeach
                </select>
                @error('category')<span class="invalid-feedback">{{ $message }}</span>@enderror
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="amount" class="font-weight-bold">Amount (Rs) <span class="text-danger">*</span></label>
                <input type="number" class="form-control @error('amount') is-invalid @enderror" name="amount" id="amount" value="{{ old('amount') }}" min="0.01" step="0.01" placeholder="0.00" required>
                @error('amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              </div>
            </div>

            <!-- Labour fields (shown only when category == Labour) -->
            <div id="labourFields" class="col-12 {{ old('category') === 'Labour' ? '' : 'd-none' }}">
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="vendor_id" class="font-weight-bold">Vendor <span class="text-danger">*</span></label>
                    <select class="form-control @error('vendor_id') is-invalid @enderror" name="vendor_id" id="vendor_id" required>
                      <option value="">-- Select Vendor --</option>
                      @foreach($vendors as $v)
                        <option value="{{ $v->id }}" {{ old('vendor_id') == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                      @endforeach
                    </select>
                    @error('vendor_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-group">
                    <label for="start_date" class="font-weight-bold">Start Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" name="start_date" id="start_date" value="{{ old('start_date') }}" required>
                    @error('start_date')<span class="invalid-feedback">{{ $message }}</span>@enderror
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-group">
                    <label for="end_date" class="font-weight-bold">End Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" name="end_date" id="end_date" value="{{ old('end_date') }}" required>
                    @error('end_date')<span class="invalid-feedback">{{ $message }}</span>@enderror
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-group">
                    <label for="total_labour" class="font-weight-bold">Total Labour <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('total_labour') is-invalid @enderror" name="total_labour" id="total_labour" value="{{ old('total_labour') }}" min="0" step="1" required>
                    @error('total_labour')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="payment_mode" class="font-weight-bold">Payment Mode <span class="text-danger">*</span></label>
                <select class="form-control @error('payment_mode') is-invalid @enderror" name="payment_mode" id="payment_mode" required>
                  <option value="">-- Select Payment Mode --</option>
                  @foreach(['cash' => 'Cash', 'online' => 'Online', 'cheque' => 'Cheque'] as $value => $label)
                    <option value="{{ $value }}" {{ old('payment_mode') == $value ? 'selected' : '' }}>{{ $label }}</option>
                  @endforeach
                </select>
                @error('payment_mode')<span class="invalid-feedback">{{ $message }}</span>@enderror
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="bill" class="font-weight-bold">Bill Upload</label>
                <div class="custom-file">
                  <input type="file" class="custom-file-input @error('bill') is-invalid @enderror" name="bill" id="bill" accept=".pdf,.jpg,.jpeg,.png">
                  <label class="custom-file-label" for="bill">Choose file...</label>
                </div>
                @error('bill')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
              </div>
            </div>
          </div>

          <hr>

          <p class="text-uppercase text-muted font-weight-bold mb-3" style="font-size:.7rem;letter-spacing:1.4px;">
            <i class="fas fa-sticky-note mr-1"></i> Additional Info
          </p>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="description" class="font-weight-bold">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" name="description" id="description" rows="4" placeholder="Add a short description of the expense, items purchased, or purpose.">{{ old('description') }}</textarea>
                <small class="text-muted d-block mt-1">Optional. Use this for supporting details.</small>
                @error('description')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="note" class="font-weight-bold">Note</label>
                <textarea class="form-control @error('note') is-invalid @enderror" name="note" id="note" rows="4" placeholder="Enter the key reason for this expense or any important internal note.">{{ old('note') }}</textarea>
                <small class="text-muted d-block mt-1">Optional. Add any helpful internal note if needed.</small>
                @error('note')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
              </div>
            </div>
          </div>
      </div>
      <div class="card-footer">
        <button type="submit" class="btn-submit">
          <i class="fas fa-save mr-1"></i>Save Expense
        </button>
        <a href="{{ route('expense.index') }}" class="btn-cancel ml-2">
          <i class="fas fa-times mr-1"></i>Cancel
        </a>
      </div>
        </form>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var el = document.getElementById('expense_date');
    var amountInput = document.getElementById('amount');
    var form = amountInput ? amountInput.form : null;
    if (el) { el.min = '{{ date('Y-m-d') }}'; }

    function getFeedbackElement(input) {
      var sibling = input.nextElementSibling;
      if (sibling && sibling.classList.contains('invalid-feedback')) {
        return sibling;
      }

      var feedback = document.createElement('span');
      feedback.className = 'invalid-feedback';
      input.insertAdjacentElement('afterend', feedback);

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

    function validateAmount() {
      if (!amountInput) {
        return true;
      }

      var value = amountInput.value.trim();
      if (!value) {
        clearError(amountInput);
        return true;
      }

      if (isNaN(value) || Number(value) <= 0) {
        setError(amountInput, 'Amount must be greater than 0.');
        return false;
      }

      clearError(amountInput);
      return true;
    }

    if (amountInput) {
      amountInput.addEventListener('input', validateAmount);
    }

    if (form) {
      form.addEventListener('submit', function (event) {
        if (!validateAmount()) {
          event.preventDefault();
        }
      });
    }

    document.getElementById('bill').addEventListener('change', function () {
      var fileName = this.files[0] ? this.files[0].name : 'Choose file...';
      this.nextElementSibling.textContent = fileName;
    });
  });
</script>
@endpush
