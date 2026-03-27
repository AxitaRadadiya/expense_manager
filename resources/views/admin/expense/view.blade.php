@extends('admin.layouts.app')
@section('title', 'Expense Details')

@section('content')

@php
  $badgeClass = match($expense->status) {
    'approved' => 'badge-success',
    'rejected' => 'badge-danger',
    default => 'badge-warning',
  };

  $icon = match($expense->status) {
    'approved' => 'fa-check-circle',
    'rejected' => 'fa-times-circle',
    default => 'fa-clock',
  };

  $billExt = $expense->bill_path ? strtolower(pathinfo($expense->bill_path, PATHINFO_EXTENSION)) : null;
@endphp

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><i class="fas fa-file-invoice mr-2 text-teal"></i>Expense Details</h1>
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
            <a href="{{ route('expense.edit', $expense->id) }}" class="btn btn-info btn-sm">
              <i class="fas fa-edit mr-1"></i>Edit
            </a>
          @endif
          <a href="{{ route('expense.index') }}" class="btn btn-default btn-sm">
            <i class="fas fa-arrow-left mr-1"></i>Back
          </a>
        </div>
      </div>

      <div class="card-body">
        <div class="row">

          <div class="col-lg-7 mb-4">
            <div class="row">
              <div class="col-md-6 mb-3">
                <div class="border rounded h-100 p-3 bg-light">
                  <small class="text-muted d-block mb-1">Project</small>
                  <div class="font-weight-bold">{{ $expense->project->name ?? '-' }}</div>
                </div>
              </div>

              <div class="col-md-6 mb-3">
                <div class="border rounded h-100 p-3 bg-light">
                  <small class="text-muted d-block mb-1">Expense Date</small>
                  <div class="font-weight-bold">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M, Y') }}</div>
                </div>
              </div>

              <div class="col-md-6 mb-3">
                <div class="border rounded h-100 p-3 bg-light">
                  <small class="text-muted d-block mb-1">Amount</small>
                  <div class="font-weight-bold text-success">Rs. {{ number_format((float) $expense->amount, 2) }}</div>
                </div>
              </div>

              <div class="col-md-6 mb-3">
                <div class="border rounded h-100 p-3 bg-light">
                  <small class="text-muted d-block mb-1">Category</small>
                  <div class="font-weight-bold">{{ $expense->category ?: '-' }}</div>
                </div>
              </div>

              <div class="col-md-6 mb-3">
                <div class="border rounded h-100 p-3 bg-light">
                  <small class="text-muted d-block mb-1">Payment Mode</small>
                  <div class="font-weight-bold">{{ $expense->payment_mode ? ucfirst($expense->payment_mode) : '-' }}</div>
                </div>
              </div>

              <div class="col-md-6 mb-3">
                <div class="border rounded h-100 p-3 bg-light">
                  <small class="text-muted d-block mb-1">Submitted By</small>
                  <div class="font-weight-bold">{{ $expense->user->name ?? '-' }}</div>
                </div>
              </div>

              <div class="col-md-6 mb-3">
                <div class="border rounded h-100 p-3 bg-light">
                  <small class="text-muted d-block mb-1">Created On</small>
                  <div class="font-weight-bold">{{ $expense->created_at ? $expense->created_at->format('d M, Y h:i A') : '-' }}</div>
                </div>
              </div>

              <div class="col-12 mb-3">
                <div class="border rounded p-3">
                  <small class="text-muted d-block mb-2">Description</small>
                  <div class="mb-0" style="min-height:60px;">{{ $expense->description ?: '-' }}</div>
                </div>
              </div>

              <div class="col-12">
                <div class="border rounded p-3">
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
              <div class="card-body text-center">
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
                  <a href="{{ asset('storage/' . $expense->bill_path) }}"
                     download="{{ $expense->bill_original_name ?? 'bill' }}"
                     class="btn btn-success btn-sm">
                    <i class="fas fa-download mr-1"></i>Download
                  </a>
                @else
                  <div class="py-5 text-muted">
                    <i class="fas fa-file-upload fa-3x mb-2 d-block text-secondary"></i>
                    <p class="mb-0">No bill uploaded.</p>
                  </div>
                @endif
              </div>
            </div>

            @can('expense-approve')
              @if($expense->status === 'pending')
                <div class="card card-outline card-warning shadow-sm">
                  <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-check mr-2"></i>Approval Action</h3>
                  </div>
                  <div class="card-body">
                    <form action="{{ route('expense.approve', $expense->id) }}" method="POST" class="mb-3">
                      @csrf
                      @method('PATCH')
                      <div class="form-group">
                        <label for="approve_remark">Approval Remark <small class="text-muted">(optional)</small></label>
                        <textarea class="form-control form-control-sm"
                                  name="remark" id="approve_remark"
                                  rows="2"
                                  placeholder="Add a remark for approval..."></textarea>
                      </div>
                      <button type="submit" class="btn btn-success btn-block">
                        <i class="fas fa-check-circle mr-1"></i>Approve Expense
                      </button>
                    </form>

                    <hr class="my-3">

                    <form action="{{ route('expense.reject', $expense->id) }}" method="POST">
                      @csrf
                      @method('PATCH')
                      <div class="form-group">
                        <label for="reject_remark">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control form-control-sm @error('remark') is-invalid @enderror"
                                  name="remark" id="reject_remark"
                                  rows="2"
                                  placeholder="Provide a reason for rejection..." required>{{ old('remark') }}</textarea>
                        @error('remark')
                          <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                      </div>
                      <button type="submit" class="btn btn-danger btn-block">
                        <i class="fas fa-times-circle mr-1"></i>Reject Expense
                      </button>
                    </form>
                  </div>
                </div>
              @endif

              @if(in_array($expense->status, ['approved', 'rejected']) && $expense->remark)
                <div class="callout callout-{{ $expense->status === 'approved' ? 'success' : 'danger' }}">
                  <p class="text-muted font-weight-bold mb-1">{{ ucfirst($expense->status) }} Remark</p>
                  <p class="mb-0">{{ $expense->remark }}</p>
                </div>
              @endif
            @endcan
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
