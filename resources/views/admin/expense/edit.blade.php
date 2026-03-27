@extends('admin.layouts.app')
@section('title', 'Edit Expense')

@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><i class="fas fa-edit mr-2 text-teal"></i>Edit Expense</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('expense.index') }}">Expenses</a></li>
          <li class="breadcrumb-item active">Edit</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="card card-outline card-teal shadow-sm">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-file-invoice mr-2"></i>Edit Expense Details</h3>
        <div class="card-tools">
          <a href="{{ route('expense.index') }}" class="btn btn-default btn-sm">
            <i class="fas fa-arrow-left mr-1"></i>Back
          </a>
        </div>
      </div>
      <div class="card-body">

        @if($errors->any())
          <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <h5><i class="icon fas fa-ban"></i> Validation Error</h5>
            <ul class="mb-0">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form action="{{ route('expense.update', $expense->id) }}" method="POST" enctype="multipart/form-data">
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

            {{-- Expense Date --}}
            <div class="col-md-6 mb-3">
              <label for="expense_date">Expense Date <span class="text-danger">*</span></label>
              <input type="date"
                     class="form-control @error('expense_date') is-invalid @enderror"
                     name="expense_date" id="expense_date"
                     value="{{ old('expense_date', \Carbon\Carbon::parse($expense->expense_date)->format('Y-m-d')) }}"
                     max="{{ date('Y-m-d') }}" required>
              @error('expense_date')
                <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>

            {{-- Expense Category --}}
            <div class="col-md-6 mb-3">
              <label for="category">Expense Category <span class="text-danger">*</span></label>
              <select class="form-control @error('category') is-invalid @enderror"
                      name="category" id="category" required>
                <option value="">-- Select Category --</option>
                @foreach($categories as $cat)
                  <option value="{{ $cat->name }}"
                    {{ old('category', $expense->category) == $cat->name ? 'selected' : '' }}>
                    {{ $cat->name }}
                  </option>
                @endforeach
              </select>
              @error('category')
                <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>

            {{-- Amount --}}
            <div class="col-md-6 mb-3">
              <label for="amount">Amount <span class="text-danger">*</span></label>
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

            {{-- Payment Mode --}}
            <div class="col-md-6 mb-3">
              <label for="payment_mode">Payment Mode <span class="text-danger">*</span></label>
              <select class="form-control @error('payment_mode') is-invalid @enderror"
                      name="payment_mode" id="payment_mode" required>
                <option value="">-- Select Payment Mode --</option>
                @foreach(['cash' => 'Cash', 'online' => 'Online', 'cheque' => 'Cheque'] as $value => $label)
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


            {{-- Bill Upload --}}
            <div class="col-md-6 mb-3">
              <label for="bill">Bill Upload</label>

              @if($expense->bill_path)
                @php $ext = strtolower(pathinfo($expense->bill_path, PATHINFO_EXTENSION)); @endphp
                <div class="callout callout-info mb-2 py-2">
                  <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                      @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                        <img src="{{ asset('storage/' . $expense->bill_path) }}"
                             alt="Current Bill"
                             class="img-thumbnail mr-2"
                             style="height:48px;width:64px;object-fit:cover;">
                      @else
                        <span class="btn btn-sm btn-danger mr-2" style="pointer-events:none;">
                          <i class="fas fa-file-pdf"></i>
                        </span>
                      @endif
                      <div>
                        <div class="font-weight-bold" style="font-size:.83rem;">Current Bill</div>
                        <small class="text-muted">
                          {{ $expense->bill_original_name ?? strtoupper($ext) . ' file' }}
                        </small>
                      </div>
                    </div>
                    <a href="{{ asset('storage/' . $expense->bill_path) }}"
                       target="_blank" class="btn btn-outline-primary btn-xs">
                      <i class="fas fa-eye mr-1"></i>View
                    </a>
                  </div>
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
              <small class="form-text text-muted">
                <i class="fas fa-info-circle mr-1"></i>
                Supported: PDF, JPG, PNG.
                @if($expense->bill_path) Leave blank to keep current bill. @endif
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

          </div>{{-- /.row --}}

      </div>
      <div class="card-footer">
        <button type="submit" class="btn btn-teal waves-effect waves-light">
          <i class="fas fa-save mr-1"></i>Update Expense
        </button>
        <a href="{{ route('expense.index') }}" class="btn btn-default waves-effect waves-light ml-2">
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
    if (el) { el.removeAttribute('min'); }

    document.getElementById('bill').addEventListener('change', function () {
      var fileName = this.files[0] ? this.files[0].name : (this.getAttribute('data-placeholder') || 'Choose file...');
      this.nextElementSibling.textContent = fileName;
    });
  });
</script>
@endpush
