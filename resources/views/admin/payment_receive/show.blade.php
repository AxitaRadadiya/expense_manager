@extends('admin.layouts.app')
@section('title', 'Payment Received Details')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mt-3">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">

                    <h1 class="m-0">
                        <i class="fas fa-file-invoice mr-2 text-primary"></i>
                        Payment Received Details
                    </h1>

                    <a href="{{ route('invoice.index') }}"
                        class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </a>

                </div>
            </div>
        </div>
    </div>
</div>
<div class="container py-5" style="min-height: 90vh;">
  <div class="justify-content-center">
    <div class="">
      <div class="card shadow border-0">
        <div class="card-body p-5">
          <div class="text-center mb-4">
            <!-- Avatar/Initial -->
            <div style="width:64px;height:64px;background:#339D9D;color:#fff;font-size:2rem;line-height:64px;border-radius:8px;display:inline-block;font-weight:bold;">
              {{ strtoupper(substr($payment->customer->name ?? 'C',0,1)) }}
            </div>
            <div class="mt-2 mb-0 h5">{{ $payment->customer->name ?? '-' }}</div>
            <div class="text-muted small">{{ $payment->customer->country ?? '' }}</div>
          </div>
          <hr>
          <h4 class="text-center mb-4 font-weight-bold">PAYMENT RECEIPT</h4>
          <div class="row align-items-center">
            <div class="col-md-8">
              <div class="row mb-2">
                <div class="col-2 text-muted">Payment Date</div>
                <div class="col-2 font-weight-bold">{{ $payment->payment_date }}</div>
              </div>
             
              <div class="row mb-2">
                <div class="col-2 text-muted">Payment Mode</div>
                <div class="col-2 font-weight-bold">{{ ucfirst($payment->payment_type) }}</div>
              </div>
             
            </div>
            <div class="col-md-4 text-center">
              <div style="background:#5cb85c;color:#fff;padding:24px 0;border-radius:8px;">
                <div class="mb-1">Amount Received</div>
                <div style="font-size:1.5rem;font-weight:bold;">₹{{ number_format($payment->amount,2) }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection