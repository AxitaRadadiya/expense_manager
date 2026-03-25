{{-- resources/views/admin/expense/edit.blade.php --}}

@extends('admin.layouts.app')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
       <div class="card-header d-flex align-items-center ">
        <h4 class="card-title mb-0">
          <i class="fa fa-edit mr-2 text-primary"></i>Edit Expense
        </h4>
      </div>
      <div class="card-body">

        @if($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form action="{{ route('expense.update', $expense->id) }}"
              method="POST"
              enctype="multipart/form-data">
          @csrf
          @method('PUT')

          <div class="row">
            {{-- Project --}}
            <div class="col-md-6 mb-3">
              <label for="projects_id">Project <span class="text-danger">*</span></label>
              <select class="form-control @error('projects_id') is-invalid @enderror"
                      name="projects_id" id="projects_id" required>
                <option value="">-- Select Project --</option>
                @foreach($projects as $project)
                  <option value="{{ $project->id }}"
                    {{ old('projects_id', $expense->projects_id) == $project->id ? 'selected' : '' }}>
                    {{ $project->name }}
                  </option>
                @endforeach
              </select>
              @error('projects_id')
                <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>

            <div class="col-md-6 mb-3">
              <label for="expense_date">Expense Date <span class="text-danger">*</span></label>
              <input type="date"
                     class="form-control @error('expense_date') is-invalid @enderror"
                     name="expense_date" id="expense_date"
                     value="{{ old('expense_date', \Carbon\Carbon::parse($expense->expense_date)->format('Y-m-d')) }}"
                     required>
              @error('expense_date')
                <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>

            <div class="col-md-6 mb-3">
              <label for="amount">Amount (₹) <span class="text-danger">*</span></label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">₹</span>
                </div>
                <input type="number"
                       class="form-control @error('amount') is-invalid @enderror"
                       name="amount" id="amount"
                       value="{{ old('amount', $expense->amount) }}"
                       min="0" step="0.01"
                       placeholder="0.00" required>
                @error('amount')
                  <span class="invalid-feedback">{{ $message }}</span>
                @enderror
              </div>
            </div>

              <div class="col-md-6 mb-3">
                <label for="category">Expense Category <span class="text-danger">*</span></label>
                <select class="form-control @error('category') is-invalid @enderror"
                        name="category" id="category" required>
                  <option value="">-- Select Category --</option>
                  @foreach($categories as $cat)
                    <option value="{{ $cat->name }}" {{ old('category', $expense->category) == $cat->name ? 'selected' : '' }}>
                      {{ $cat->name }}
                    </option>
                  @endforeach
                </select>
                @error('category')
                  <span class="invalid-feedback">{{ $message }}</span>
                @enderror
              </div>
            <div class="col-md-6 mb-3">
              <label for="payment_mode">Payment Mode <span class="text-danger">*</span></label>
              <select class="form-control @error('payment_mode') is-invalid @enderror"
                      name="payment_mode" id="payment_mode" required>
                <option value="">-- Select Payment Mode --</option>
                @foreach(['cash' => 'Cash', 'online' => 'online', 'cheque' => 'Cheque'] as $value => $label)
                  <option value="{{ $value }}"
                    {{ old('payment_mode', $expense->payment_mode) == $value ? 'selected' : '' }}>
                    {{ $label }}
                  </option>
                @endforeach
              </select>
              @error('payment_mode')
                <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>

            {{-- Reference Number --}}
            <div class="col-md-6 mb-3">
              <label for="reference_number">Reference Number</label>
              <input type="text"
                     class="form-control @error('reference_number') is-invalid @enderror"
                     name="reference_number" id="reference_number"
                     value="{{ old('reference_number', $expense->reference_number) }}"
                     placeholder="Enter reference number">
              @error('reference_number')
                <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>

            <div class="col-md-6 mb-3">
              <label for="bill">Bill Upload</label>

              @if($expense->bill_path)
                @php $ext = strtolower(pathinfo($expense->bill_path, PATHINFO_EXTENSION)); @endphp
                <div class="mb-2 p-2 border rounded d-flex align-items-center justify-content-between"
                     style="background:#f8f9fa;">
                  <div class="d-flex align-items-center" style="gap:10px;">

                    @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                      <img src="{{ asset('storage/' . $expense->bill_path) }}"
                           alt="Current Bill"
                           style="height:48px; width:64px; object-fit:cover;
                                  border-radius:4px; border:1px solid #dee2e6;">
                    @else
                      <div style="height:48px; width:48px; background:#fff3cd;
                                  border-radius:4px; border:1px solid #ffc107;
                                  display:flex; align-items:center; justify-content:center;">
                        <i class="fa fa-file-pdf text-danger fa-lg"></i>
                      </div>
                    @endif

                    <div>
                      <p class="mb-0 font-weight-bold text-dark" style="font-size:13px;">
                        Current Bill
                      </p>
                      <small class="text-muted">
                        {{ $expense->bill_original_name ?? strtoupper($ext) . ' file' }}
                      </small>
                    </div>
                  </div>

                  <a href="{{ asset('storage/' . $expense->bill_path) }}"
                     target="_blank"
                     class="btn btn-outline-primary btn-sm">
                    <i class="fa fa-eye mr-1"></i> View
                  </a>
                </div>
              @endif

              <div class="custom-file">
                <input type="file"
                       class="custom-file-input @error('bill') is-invalid @enderror"
                       name="bill" id="bill"
                       accept=".pdf,.jpg,.jpeg,.png">
                <label class="custom-file-label" for="bill">
                  {{ $expense->bill_path ? 'Replace file...' : 'Choose file...' }}
                </label>
              </div>
              <small class="text-muted d-block mt-1">
                <i class="fa fa-info-circle"></i>
                Supported: PDF, JPG, PNG.
                @if($expense->bill_path)
                  Leave blank to keep the current bill.
                @endif
              </small>
              @error('bill')
                <span class="text-danger small">{{ $message }}</span>
              @enderror
            </div>

            <div class="col-12">
              <hr class="mt-2 mb-3">
            </div>

            <div class="col-md-6 mb-3">
              <label for="description">Description</label>
              <textarea class="form-control @error('description') is-invalid @enderror"
                        name="description" id="description"
                        rows="4"
                        placeholder="Add a short description of the expense, items purchased, or purpose.">{{ old('description', $expense->description) }}</textarea>
              <small class="text-muted d-block mt-1">Optional. Use this for supporting details.</small>
              @error('description')
                <span class="invalid-feedback d-block">{{ $message }}</span>
              @enderror
            </div>

            <div class="col-md-6 mb-3">
              <label for="note">Note <span class="text-danger">*</span></label>
              <textarea class="form-control @error('note') is-invalid @enderror"
                        name="note" id="note"
                        rows="4"
                        placeholder="Enter the key reason for this expense or any important internal note." required>{{ old('note', $expense->note) }}</textarea>
              <small class="text-muted d-block mt-1">Required. This note will help identify the expense later.</small>
              @error('note')
                <span class="invalid-feedback d-block">{{ $message }}</span>
              @enderror
            </div>
          </div>

          <div class="mt-2">
            <button type="submit" class="btn btn-primary waves-effect waves-light">
              <i class="fa fa-save mr-1"></i> Update Expense
            </button>
            <a href="{{ route('expense.index') }}"
               class="btn btn-secondary waves-effect waves-light ml-2">
              <i class="fa fa-times mr-1"></i> Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
