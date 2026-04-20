@extends('admin.layouts.app')
@section('title', 'Expense Details')

@section('content')

@php
$billExt = $expense->bill_path ? strtolower(pathinfo($expense->bill_path, PATHINFO_EXTENSION)) : null;
@endphp

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><i class="mr-2 text-teal"></i>Expense Details</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('expense.index') }}">Expenses</a></li>
          <li class="breadcrumb-item active">View</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">

    <div class="card card-outline card-teal shadow-sm">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-receipt mr-2"></i>Expense Summary
        </h3>
        <div class="card-tools d-flex align-items-center" style="gap:.5rem;">
          @if(auth()->check() && method_exists(auth()->user(), 'hasRole') && auth()->user()->hasRole('super-admin'))
          <a href="{{ route('expense.edit', $expense->id) }}" class="btn-submit">
            <i class="fas fa-edit mr-1"></i>Edit
          </a>
          @endif
          <a href="{{ route('expense.index') }}" class="btn-cancel">
            <i class="fas fa-arrow-left mr-1"></i>Back
          </a>
        </div>
      </div>

      <div class="card-body">
        <div class="row">

          <div class="col-lg-7 mb-4">
            <div class="border rounded h-100 p-3 bg-light">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <small class="text-muted d-block mb-1">Project</small>
                  <div class="font-weight-bold">{{ $expense->project->name ?? '-' }}</div>
                </div>

                <div class="col-md-6 mb-3">
                  <small class="text-muted d-block mb-1">Expense Date</small>
                  <div class="font-weight-bold">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d-m-Y') }}</div>
                </div>

                <div class="col-md-6 mb-3">
                  <small class="text-muted d-block mb-1">Amount</small>
                  <div class="font-weight-bold text-success">Rs. {{ number_format((float) $expense->amount, 2) }}</div>
                </div>

                <div class="col-md-6 mb-3">
                  <small class="text-muted d-block mb-1">Category</small>
                  <div class="font-weight-bold">{{ $expense->category ?: '-' }}</div>
                </div>

                @if(($expense->category ?? null) === 'Labour')
                <div class="col-md-6 mb-3">
                  <small class="text-muted d-block mb-1">Vendor</small>
                  <div class="font-weight-bold">{{ $expense->vendor->name ?? '-' }}</div>
                </div>

                <div class="col-md-6 mb-3">
                  <small class="text-muted d-block mb-1">Start Date</small>
                  <div class="font-weight-bold">{{ $expense->start_date ? \Carbon\Carbon::parse($expense->start_date)->format('d-m-Y') : '-' }}</div>
                </div>

                <div class="col-md-6 mb-3">
                  <small class="text-muted d-block mb-1">End Date</small>
                  <div class="font-weight-bold">{{ $expense->end_date ? \Carbon\Carbon::parse($expense->end_date)->format('d-m-Y') : '-' }}</div>
                </div>

                <div class="col-md-6 mb-3">
                  <small class="text-muted d-block mb-1">Total Labour</small>
                  <div class="font-weight-bold">{{ $expense->total_labour !== null ? $expense->total_labour : '-' }}</div>
                </div>
                @endif

                <div class="col-md-6 mb-3">
                  <small class="text-muted d-block mb-1">Payment Mode</small>
                  <div class="font-weight-bold">{{ $expense->payment_mode ? ucfirst($expense->payment_mode) : '-' }}</div>
                </div>

                <div class="col-md-6 mb-3">
                  <small class="text-muted d-block mb-1">Submitted By</small>
                  <div class="font-weight-bold">{{ $expense->user->name ?? '-' }}</div>
                </div>

                <div class="col-md-6 mb-3">
                  <small class="text-muted d-block mb-1">Created On</small>
                  <div class="font-weight-bold">{{ $expense->created_at ? $expense->created_at->format('d-m-Y h:i A') : '-' }}</div>
                </div>

                <div class="col-12 mb-3">
                  <small class="text-muted d-block mb-2">Description</small>
                  <div class="mb-0" style="min-height:60px;">{{ $expense->description ?: '-' }}</div>
                </div>

                <div class="col-12">
                  <small class="text-muted d-block mb-2">Note</small>
                  <div class="mb-0" style="min-height:60px;">{{ $expense->note ?: '-' }}</div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-5 mb-4">
            <div class="card card-outline card-teal h-100 mb-3">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-file-alt mr-2"></i>Bill / Receipt</h3>
              </div>
              <div class="card-body text-center bill-upload">
                @if($expense->bill_path)
                @if(in_array($billExt, ['jpg', 'jpeg', 'png']))
                <img src="{{ asset('storage/' . $expense->bill_path) }}"
                  alt="Bill"
                  class="img-fluid img-thumbnail mb-3"
                  style="max-height:320px;cursor:zoom-in;"
                  onclick="showBillImage('{{ asset('storage/' . $expense->bill_path) }}')">
                @elseif($billExt === 'pdf')
                <div class="border rounded mb-3" style="height:320px;overflow:hidden;">
                  <iframe src="{{ asset('storage/' . $expense->bill_path) }}" width="100%" height="100%" style="border:none;"></iframe>
                </div>
                @else
                <div class="py-4 text-muted">
                  <i class="fas fa-file fa-3x mb-2 d-block text-secondary"></i>
                  <p class="mb-0">Preview not available.</p>
                </div>
                @endif

                <div class="text-muted small mb-3">
                  {{ $expense->bill_original_name ?? 'Uploaded bill' }}
                </div>

                <a href="{{ asset('storage/' . $expense->bill_path) }}"
                  target="_blank"
                  class="btn btn-outline-primary btn-sm mr-2">
                  <i class="fas fa-eye mr-1"></i>Open
                </a>
                <!-- <a href="{{ asset('storage/' . $expense->bill_path) }}"
                     download="{{ $expense->bill_original_name ?? 'bill' }}"
                     class="btn btn-success btn-sm">
                    <i class="fas fa-download mr-1"></i>Download
                  </a> -->
                @else
                <div class="py-5 text-muted">
                  <i class="fas fa-file-upload fa-3x mb-2 d-block text-secondary"></i>
                  <p class="mb-0">No bill uploaded.</p>
                </div>
                @endif
              </div>
            </div>

          </div>

        </div>
      </div>
    </div>

  </div>
</section>

<div class="modal fade" id="billImageModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-file-image mr-2 text-teal"></i>Bill Preview
        </h5>
        <button type="button" class="close" data-dismiss="modal">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center p-2">
        <img id="billModalImage" src="" class="img-fluid rounded" alt="Bill">
      </div>
      <div class="modal-footer">
        <a id="billModalDownload" href="" download class="btn btn-success btn-sm">
          <i class="fas fa-download mr-1"></i>Download
        </a>
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
          <i class="fas fa-times mr-1"></i>Close
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
  function showBillImage(src) {
    document.getElementById('billModalImage').src = src;
    document.getElementById('billModalDownload').href = src;
    $('#billImageModal').modal('show');
  }
</script>
@endpush