@extends('admin.layouts.app')
@section('title', 'Credit Details')

@section('content')
<div class="content-header">
  <div class="container-fluid-80">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><i class="mr-2 text-success"></i>Credit Details</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('credit.index') }}">Credits</a></li>
          <li class="breadcrumb-item active">View</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid-80">
    <div class="card card-outline card-success shadow-sm">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-hand-holding-usd mr-2"></i>Credit Summary</h3>
        <div class="card-tools">
          <a href="{{ route('credit.edit', $credit->id) }}" class="btn-submit">
            <i class="fas fa-edit mr-1"></i>Edit
          </a>
          <a href="{{ route('credit.index') }}" class="btn-cancel ml-1">
            <i class="fas fa-arrow-left mr-1"></i>Back
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="border rounded p-3 bg-light w-100">
          <div class="row">
            <div class="col-md-6 mb-3">
              <small class="text-muted d-block mb-1">Project</small>
              <div class="font-weight-bold">{{ optional($credit->project)->name ?? '-' }}</div>
            </div>
            <div class="col-md-6 mb-3">
              <small class="text-muted d-block mb-1">Credit Date</small>
              <div class="font-weight-bold">{{ optional($credit->credit_date)->format('d-m-Y') ?? '-' }}</div>
            </div>
            <div class="col-md-6 mb-3">
              <small class="text-muted d-block mb-1">Amount</small>
              <div class="font-weight-bold text-success">Rs. {{ number_format((float) $credit->amount, 2) }}</div>
            </div>
            <div class="col-md-6 mb-3">
              <small class="text-muted d-block mb-1">Created By</small>
              <div class="font-weight-bold">{{ optional($credit->user)->name ?? '-' }}</div>
            </div>
            <div class="col-md-6 mb-3">
              <small class="text-muted d-block mb-1">Created On</small>
              <div class="font-weight-bold">{{ $credit->created_at ? $credit->created_at->format('d-m-Y h:i A') : '-' }}</div>
            </div>
            <div class="col-md-6 mb-3">
              <small class="text-muted d-block mb-1">Payment Mode</small>
              <div class="font-weight-bold text-capitalize">{{ $credit->payment_mode ?: '-' }}</div>
            </div>
            <!-- <div class="col-md-6 mb-3">
              <small class="text-muted d-block mb-1">Bill</small>
              <div class="font-weight-bold">
                @if($credit->bill_path)
                  <a href="{{ asset('storage/' . $credit->bill_path) }}" target="_blank">{{ $credit->bill_original_name ?: 'View Uploaded Bill' }}</a>
                @else
                  -
                @endif
              </div>
          </div> -->
            <div class="col-12 mb-3">
              <small class="text-muted d-block mb-2">Description</small>
              <div>{{ $credit->description ?: '-' }}</div>
            </div>
            <div class="col-12">
              <small class="text-muted d-block mb-2">Note</small>
              <div>{{ $credit->note ?: '-' }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection