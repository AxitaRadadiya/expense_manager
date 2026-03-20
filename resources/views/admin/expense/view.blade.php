{{-- resources/views/admin/expense/view.blade.php --}}

@extends('admin.layouts.app')

@section('content')

<div class="row">
  <div class="col-12">

    {{-- ─── Page Header Card ─────────────────────────────────────────────── --}}
    <div class="card">
      <div class="card-header">
        <div class="row w-100 align-items-center">
          <div class="col">
            <h4 class="card-title mb-0">
              <i class="fa fa-file-invoice mr-2 text-primary"></i>Expense Details
            </h4>
          </div>
          <div class="col text-right">
            <div class="d-flex flex-wrap justify-content-end" style="gap:8px;">

              {{-- Back --}}
              <a href="{{ route('expense.index') }}"
                 class="btn btn-secondary btn-sm waves-effect waves-light">
                <i class="fa fa-arrow-left mr-1"></i> Back
              </a>

              {{-- Edit --}}
              @if($expense->status === 'pending')
                <a href="{{ route('expense.edit', $expense->id) }}"
                   class="btn btn-info btn-sm waves-effect waves-light">
                  <i class="fa fa-edit mr-1"></i> Edit
                </a>
              @endif

              {{-- Download Bill --}}
              @if($expense->bill_path)
                <a href="{{ asset('storage/' . $expense->bill_path) }}"
                   download
                   class="btn btn-success btn-sm waves-effect waves-light">
                  <i class="fa fa-download mr-1"></i> Download Bill
                </a>
              @endif

              {{-- Delete --}}
              <form action="{{ route('expense.destroy', $expense->id) }}"
                    method="POST"
                    data-delete-type="expense"
                    class="d-inline">
                @csrf
                @method('DELETE')
                <button type="button"
                        class="btn btn-danger btn-sm waves-effect waves-light deleteButton">
                  <i class="fa fa-trash mr-1"></i> Delete
                </button>
              </form>

            </div>
          </div>
        </div>
      </div>
    </div>
    {{-- ─── End Page Header ──────────────────────────────────────────────── --}}

    <div class="row">

      {{-- ══ LEFT PANEL — Expense Details ═══════════════════════════════════ --}}
      <div class="col-lg-7 col-md-12 mb-4">
        <div class="card h-100">
          <div class="card-header">
            <h5 class="card-title mb-0">
              <i class="fa fa-info-circle mr-2 text-primary"></i>Expense Information
            </h5>
          </div>
          <div class="card-body">

            {{-- ─── Status Badge ─────────────────────────────────────────── --}}
            <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
              <span class="text-muted"
                    style="font-size:13px; font-weight:600;
                           text-transform:uppercase; letter-spacing:.6px;">
                Current Status
              </span>
              @php
                $badge = match($expense->status) {
                  'approved' => 'success',
                  'rejected' => 'danger',
                  default    => 'warning',
                };
                $icon = match($expense->status) {
                  'approved' => 'fa-check-circle',
                  'rejected' => 'fa-times-circle',
                  default    => 'fa-clock',
                };
              @endphp
              <span class="badge badge-{{ $badge }}"
                    style="font-size:13px; padding:6px 16px; border-radius:20px;">
                <i class="fa {{ $icon }} mr-1"></i>
                {{ ucfirst($expense->status) }}
              </span>
            </div>

            {{-- ─── Detail Rows ───────────────────────────────────────────── --}}
            <div class="row">

              {{-- Project --}}
              <div class="col-sm-6 mb-4">
                <p class="text-muted mb-1"
                   style="font-size:12px; font-weight:600;
                          text-transform:uppercase; letter-spacing:.6px;">
                  <i class="fa fa-project-diagram mr-1 text-primary"></i>Project
                </p>
                <p class="mb-0 font-weight-bold text-dark" style="font-size:15px;">
                  {{ $expense->project->name ?? '—' }}
                </p>
              </div>

              {{-- Expense Date --}}
              <div class="col-sm-6 mb-4">
                <p class="text-muted mb-1"
                   style="font-size:12px; font-weight:600;
                          text-transform:uppercase; letter-spacing:.6px;">
                  <i class="fa fa-calendar-alt mr-1 text-primary"></i>Expense Date
                </p>
                <p class="mb-0 font-weight-bold text-dark" style="font-size:15px;">
                  {{ \Carbon\Carbon::parse($expense->expense_date)->format('d M, Y') }}
                </p>
              </div>

              {{-- Amount --}}
              <div class="col-sm-6 mb-4">
                <p class="text-muted mb-1"
                   style="font-size:12px; font-weight:600;
                          text-transform:uppercase; letter-spacing:.6px;">
                  <i class="fa fa-rupee-sign mr-1 text-primary"></i>Amount
                </p>
                <p class="mb-0 font-weight-bold text-success" style="font-size:20px;">
                  ₹{{ number_format($expense->amount, 2) }}
                </p>
              </div>

              {{-- Payment Mode --}}
              <div class="col-sm-6 mb-4">
                <p class="text-muted mb-1"
                   style="font-size:12px; font-weight:600;
                          text-transform:uppercase; letter-spacing:.6px;">
                  <i class="fa fa-credit-card mr-1 text-primary"></i>Payment Mode
                </p>
                <p class="mb-0 font-weight-bold text-dark" style="font-size:15px;">
                  {{-- ↓ ucfirst to display "Cash" instead of "cash" --}}
                  {{ $expense->payment_mode ? ucfirst($expense->payment_mode) : '—' }}
                </p>
              </div>

              {{-- Reference Number --}}
              <div class="col-sm-6 mb-4">
                <p class="text-muted mb-1"
                   style="font-size:12px; font-weight:600;
                          text-transform:uppercase; letter-spacing:.6px;">
                  <i class="fa fa-hashtag mr-1 text-primary"></i>Reference Number
                </p>
                <p class="mb-0 font-weight-bold text-dark" style="font-size:15px;">
                  {{ $expense->reference_number ?? '—' }}
                </p>
              </div>

              {{-- Submitted By --}}
              <div class="col-sm-6 mb-4">
                <p class="text-muted mb-1"
                   style="font-size:12px; font-weight:600;
                          text-transform:uppercase; letter-spacing:.6px;">
                  <i class="fa fa-user mr-1 text-primary"></i>Submitted By
                </p>
                <p class="mb-0 font-weight-bold text-dark" style="font-size:15px;">
                  {{ $expense->user->name ?? '—' }}
                </p>
              </div>

              {{-- Description --}}
              <div class="col-sm-12 mb-2">
                <p class="text-muted mb-1"
                   style="font-size:12px; font-weight:600;
                          text-transform:uppercase; letter-spacing:.6px;">
                  <i class="fa fa-align-left mr-1 text-primary"></i>Description
                </p>
                <p class="mb-0 text-dark" style="font-size:15px; line-height:1.7;">
                  {{ $expense->description ?? '—' }}
                </p>
              </div>

            </div>{{-- end .row --}}

          </div>
        </div>
      </div>
      {{-- ══ END LEFT PANEL ══════════════════════════════════════════════════ --}}

      {{-- ══ RIGHT PANEL — Bill Preview + Approval ══════════════════════════ --}}
      <div class="col-lg-5 col-md-12 mb-4">

        {{-- ─── Bill Preview Card ────────────────────────────────────────── --}}
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="card-title mb-0">
              <i class="fa fa-file-alt mr-2 text-primary"></i>Bill / Receipt
            </h5>
          </div>
          <div class="card-body text-center p-3">

           
            @if(!empty($expense->bill_path))
              @php
                $ext = strtolower(pathinfo($expense->bill_path, PATHINFO_EXTENSION));
              @endphp

              {{-- ── Image file (JPG / PNG) ─────────────────────────────── --}}
              @if(in_array($ext, ['jpg', 'jpeg', 'png']))

                <img src="{{ asset('storage/' . $expense->bill_path) }}"
                     alt="Bill"
                     class="img-fluid rounded img-thumbnail"
                     style="max-height:340px; width:100%;
                            object-fit:contain; cursor:zoom-in;"
                     onclick="showBillImage('{{ asset('storage/' . $expense->bill_path) }}')">

                <p class="text-muted mt-2 mb-0" style="font-size:12px;">
                  <i class="fa fa-search-plus mr-1"></i>
                  Click image to open full size
                </p>

              {{-- ── PDF file ────────────────────────────────────────────── --}}
              @elseif($ext === 'pdf')

                <div class="border rounded" style="height:340px; overflow:hidden;">
                  <iframe src="{{ asset('storage/' . $expense->bill_path) }}"
                          width="100%"
                          height="100%"
                          style="border:none;">
                  </iframe>
                </div>
                <p class="text-muted mt-2 mb-0" style="font-size:12px;">
                  <i class="fa fa-file-pdf mr-1 text-danger"></i>PDF Preview
                </p>

              {{-- ── Unknown file type ───────────────────────────────────── --}}
              @else
                <div class="py-5 text-muted">
                  <i class="fa fa-file fa-4x mb-3 text-secondary"></i>
                  <p class="mb-0">Preview not available for this file type.</p>
                </div>
              @endif

              {{-- ── Original filename ───────────────────────────────────── --}}
              @if($expense->bill_original_name)
                <p class="text-muted mt-1 mb-0" style="font-size:12px;">
                  <i class="fa fa-paperclip mr-1"></i>
                  {{ $expense->bill_original_name }}
                </p>
              @endif

              {{-- ── Download button ─────────────────────────────────────── --}}
              <a href="{{ asset('storage/' . $expense->bill_path) }}"
                 download="{{ $expense->bill_original_name ?? 'bill' }}"
                 class="btn btn-success btn-sm mt-3 waves-effect waves-light">
                <i class="fa fa-download mr-1"></i> Download Bill
              </a>

            @else
              {{-- No bill uploaded --}}
              <div class="py-5 text-muted">
                <i class="fa fa-file-upload fa-4x mb-3 text-secondary"></i>
                <p class="mb-0">No bill uploaded.</p>
              </div>
            @endif

          </div>
        </div>
        {{-- ─── End Bill Preview Card ────────────────────────────────────── --}}

        {{-- ─── Approve / Reject Card ───────────────────────────────────── --}}
        @can('expense-approve')
          @if($expense->status === 'pending')
            <div class="card" style="box-shadow:0 2px 12px rgba(0,0,0,.08);">
              <div class="card-header">
                <h5 class="card-title mb-0">
                  <i class="fa fa-user-check mr-2 text-primary"></i>Approval Action
                </h5>
              </div>
              <div class="card-body">

                {{-- Approve --}}
                <form action="{{ route('expense.approve', $expense->id) }}"
                      method="POST" class="mb-3">
                  @csrf
                  @method('PATCH')
                  <div class="form-group mb-2">
                    <label for="approve_remark" class="font-weight-bold">
                      Approval Remark
                      <span class="text-muted font-weight-normal">(optional)</span>
                    </label>
                    <textarea class="form-control"
                              name="remark"
                              id="approve_remark"
                              rows="2"
                              placeholder="Add a remark for approval..."></textarea>
                  </div>
                  <button type="submit"
                          class="btn btn-success btn-block waves-effect waves-light">
                    <i class="fa fa-check-circle mr-1"></i> Approve Expense
                  </button>
                </form>

                <hr class="my-3">

                {{-- Reject --}}
                <form action="{{ route('expense.reject', $expense->id) }}"
                      method="POST">
                  @csrf
                  @method('PATCH')
                  <div class="form-group mb-2">
                    <label for="reject_remark" class="font-weight-bold">
                      Rejection Reason <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control @error('remark') is-invalid @enderror"
                              name="remark"
                              id="reject_remark"
                              rows="2"
                              placeholder="Provide a reason for rejection..."
                              required>{{ old('remark') }}</textarea>
                    @error('remark')
                      <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                  </div>
                  <button type="submit"
                          class="btn btn-danger btn-block waves-effect waves-light">
                    <i class="fa fa-times-circle mr-1"></i> Reject Expense
                  </button>
                </form>

              </div>
            </div>
          @endif

          {{-- Show remark if already actioned --}}
          @if(in_array($expense->status, ['approved', 'rejected']) && $expense->remark)
            <div class="card mt-3"
                 style="border-left: 4px solid
                        {{ $expense->status === 'approved' ? '#28a745' : '#dc3545' }};">
              <div class="card-body py-3">
                <p class="text-muted mb-1"
                   style="font-size:12px; font-weight:600;
                          text-transform:uppercase; letter-spacing:.6px;">
                  <i class="fa fa-comment-alt mr-1"></i>
                  {{ ucfirst($expense->status) }} Remark
                </p>
                <p class="mb-0 text-dark" style="font-size:14px;">
                  {{ $expense->remark }}
                </p>
              </div>
            </div>
          @endif

        @endcan

      </div>
      {{-- ══ END RIGHT PANEL ════════════════════════════════════════════════ --}}

    </div>{{-- end .row --}}

  </div>
</div>

{{-- ─── Bill Image Zoom Modal ────────────────────────────────────────────── --}}
<div class="modal fade" id="billImageModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fa fa-file-image mr-2 text-primary"></i>Bill Preview
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center p-2">
        <img id="billModalImage" src="" class="img-fluid rounded" alt="Bill">
      </div>
      <div class="modal-footer">
        <a id="billModalDownload"
           href=""
           download
           class="btn btn-success btn-sm">
          <i class="fa fa-download mr-1"></i> Download
        </a>
        <button type="button"
                class="btn btn-secondary btn-sm"
                data-dismiss="modal">
          <i class="fa fa-times mr-1"></i> Close
        </button>
      </div>
    </div>
  </div>
</div>
{{-- ─── End Modal ────────────────────────────────────────────────────────── --}}

@endsection

@section('scripts')
<script>
  // Opens the bill image in a full-size modal (same pattern as item showImage())
  function showBillImage(src) {
      document.getElementById('billModalImage').src    = src;
      document.getElementById('billModalDownload').href = src;
      $('#billImageModal').modal('show');
  }
</script>
@endsection