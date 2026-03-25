@extends('admin.layouts.app')
@section('title', 'Expense Details')

@section('content')

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

    {{-- Action buttons row --}}
    <div class="mb-3 d-flex" style="gap:.5rem;">
      <a href="{{ route('expense.index') }}" class="btn btn-default btn-sm">
        <i class="fas fa-arrow-left mr-1"></i>Back
      </a>
      @if($expense->status === 'pending')
        <a href="{{ route('expense.edit', $expense->id) }}" class="btn btn-info btn-sm">
          <i class="fas fa-edit mr-1"></i>Edit
        </a>
      @endif
    </div>

    <div class="row">

      {{-- ── LEFT: Expense Info ── --}}
      <div class="col-lg-7 col-md-12 mb-4">
        <div class="card card-outline card-teal shadow-sm h-100">
          <div class="card-header">
            <h3 class="card-title">
              <i class="fas fa-info-circle mr-2"></i>Expense Information
            </h3>
            <div class="card-tools">
              @php
                $badgeClass = match($expense->status) {
                  'approved' => 'badge-success',
                  'rejected' => 'badge-danger',
                  default    => 'badge-warning',
                };
                $icon = match($expense->status) {
                  'approved' => 'fa-check-circle',
                  'rejected' => 'fa-times-circle',
                  default    => 'fa-clock',
                };
              @endphp
              <span class="badge {{ $badgeClass }} badge-lg" style="font-size:.85rem;padding:.4rem .9rem;">
                <i class="fas {{ $icon }} mr-1"></i>{{ ucfirst($expense->status) }}
              </span>
            </div>
          </div>
          <div class="card-body">

            <div class="row">

              <div class="col-sm-6 mb-3">
                <div class="description-block border-right">
                  <span class="description-header">{{ $expense->project->name ?? '—' }}</span>
                  <span class="description-text text-uppercase">
                    <i class="fas fa-project-diagram mr-1 text-teal"></i>Project
                  </span>
                </div>
              </div>

              <div class="col-sm-6 mb-3">
                <div class="description-block border-right">
                  <span class="description-header">
                    {{ \Carbon\Carbon::parse($expense->expense_date)->format('d M, Y') }}
                  </span>
                  <span class="description-text text-uppercase">
                    <i class="fas fa-calendar-alt mr-1 text-teal"></i>Expense Date
                  </span>
                </div>
              </div>

              <div class="col-sm-6 mb-3">
                <div class="description-block border-right">
                  <span class="description-header text-success" style="font-size:1.4rem;">
                    ₹{{ number_format($expense->amount, 2) }}
                  </span>
                  <span class="description-text text-uppercase">
                    <i class="fas fa-rupee-sign mr-1 text-teal"></i>Amount
                  </span>
                </div>
              </div>

              <div class="col-sm-6 mb-3">
                <div class="description-block">
                  <span class="description-header">
                    {{ $expense->payment_mode ? ucfirst($expense->payment_mode) : '—' }}
                  </span>
                  <span class="description-text text-uppercase">
                    <i class="fas fa-credit-card mr-1 text-teal"></i>Payment Mode
                  </span>
                </div>
              </div>

              <div class="col-sm-6 mb-3">
                <div class="description-block border-right">
                  <span class="description-header">{{ $expense->reference_number ?? '—' }}</span>
                  <span class="description-text text-uppercase">
                    <i class="fas fa-hashtag mr-1 text-teal"></i>Reference Number
                  </span>
                </div>
              </div>

              <div class="col-sm-6 mb-3">
                <div class="description-block">
                  <span class="description-header">{{ $expense->user->name ?? '—' }}</span>
                  <span class="description-text text-uppercase">
                    <i class="fas fa-user mr-1 text-teal"></i>Submitted By
                  </span>
                </div>
              </div>

              <div class="col-sm-12 mt-2">
                <label class="text-uppercase text-muted font-weight-bold" style="font-size:.72rem;letter-spacing:.8px;">
                  <i class="fas fa-align-left mr-1 text-teal"></i>Description
                </label>
                <p class="text-dark mb-0" style="line-height:1.7;">
                  {{ $expense->description ?? '—' }}
                </p>
              </div>

            </div>

          </div>
        </div>
      </div>

      {{-- ── RIGHT: Bill + Approval ── --}}
      <div class="col-lg-5 col-md-12 mb-4">

        {{-- Bill Preview --}}
        <div class="card card-outline card-teal shadow-sm mb-3">
          <div class="card-header">
            <h3 class="card-title">
              <i class="fas fa-file-alt mr-2"></i>Bill / Receipt
            </h3>
          </div>
          <div class="card-body text-center p-3">

            @if(!empty($expense->bill_path))
              @php $ext = strtolower(pathinfo($expense->bill_path, PATHINFO_EXTENSION)); @endphp

              @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                <img src="{{ asset('storage/' . $expense->bill_path) }}"
                     alt="Bill"
                     class="img-fluid img-thumbnail"
                     style="max-height:300px;cursor:zoom-in;"
                     onclick="showBillImage('{{ asset('storage/' . $expense->bill_path) }}')">
                <p class="text-muted mt-2 mb-0" style="font-size:.78rem;">
                  <i class="fas fa-search-plus mr-1"></i>Click to open full size
                </p>

              @elseif($ext === 'pdf')
                <div class="border rounded" style="height:300px;overflow:hidden;">
                  <iframe src="{{ asset('storage/' . $expense->bill_path) }}"
                          width="100%" height="100%" style="border:none;"></iframe>
                </div>
                <p class="text-muted mt-2 mb-0" style="font-size:.78rem;">
                  <i class="fas fa-file-pdf mr-1 text-danger"></i>PDF Preview
                </p>

              @else
                <div class="py-4 text-muted">
                  <i class="fas fa-file fa-3x mb-2 d-block text-secondary"></i>
                  <p class="mb-0">Preview not available.</p>
                </div>
              @endif

              @if($expense->bill_original_name)
                <p class="text-muted mt-1 mb-2" style="font-size:.78rem;">
                  <i class="fas fa-paperclip mr-1"></i>{{ $expense->bill_original_name }}
                </p>
              @endif

              <a href="{{ asset('storage/' . $expense->bill_path) }}"
                 download="{{ $expense->bill_original_name ?? 'bill' }}"
                 class="btn btn-success btn-sm">
                <i class="fas fa-download mr-1"></i>Download Bill
              </a>

            @else
              <div class="py-4 text-muted">
                <i class="fas fa-file-upload fa-3x mb-2 d-block text-secondary"></i>
                <p class="mb-0">No bill uploaded.</p>
              </div>
            @endif

          </div>
        </div>

        {{-- Approval Card --}}
        @can('expense-approve')
          @if($expense->status === 'pending')
            <div class="card card-outline card-warning shadow-sm">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-user-check mr-2"></i>Approval Action
                </h3>
              </div>
              <div class="card-body">

                {{-- Approve --}}
                <form action="{{ route('expense.approve', $expense->id) }}" method="POST" class="mb-3">
                  @csrf
                  @method('PATCH')
                  <div class="form-group">
                    <label for="approve_remark" class="font-weight-bold">
                      Approval Remark
                      <small class="text-muted font-weight-normal">(optional)</small>
                    </label>
                    <textarea class="form-control form-control-sm"
                              name="remark" id="approve_remark"
                              rows="2"
                              placeholder="Add a remark for approval..."></textarea>
                  </div>
                  <button type="submit" class="btn btn-success btn-block">
                    <i class="fas fa-check-circle mr-1"></i>Approve Expense
                  </button>
                </form>

                <div class="divider">
                  <hr class="my-3">
                </div>

                {{-- Reject --}}
                <form action="{{ route('expense.reject', $expense->id) }}" method="POST">
                  @csrf
                  @method('PATCH')
                  <div class="form-group">
                    <label for="reject_remark" class="font-weight-bold">
                      Rejection Reason <span class="text-danger">*</span>
                    </label>
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

          {{-- Remark display after action --}}
          @if(in_array($expense->status, ['approved', 'rejected']) && $expense->remark)
            <div class="callout callout-{{ $expense->status === 'approved' ? 'success' : 'danger' }}">
              <p class="text-uppercase text-muted font-weight-bold mb-1" style="font-size:.72rem;letter-spacing:.8px;">
                <i class="fas fa-comment-alt mr-1"></i>{{ ucfirst($expense->status) }} Remark
              </p>
              <p class="mb-0">{{ $expense->remark }}</p>
            </div>
          @endif

        @endcan

      </div>

    </div>{{-- /.row --}}

  </div>
</section>

{{-- Bill Image Modal --}}
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