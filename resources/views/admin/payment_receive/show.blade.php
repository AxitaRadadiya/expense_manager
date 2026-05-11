@extends('admin.layouts.app')
@section('title', 'Payment Received Details')

@section('content')
<div class="content-header">
  <div class="container-fluid-80">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1><i class="mr-2 text-teal"></i>Payment Received Details</h1>
      </div>
      <div class="col-sm-6 text-right">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('payment-receive.index') }}">Payments Received</a></li>
          <li class="breadcrumb-item active">View</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="container-fluid-80">
  <div class="card card-outline card-primary shadow-sm">
    <div class="card-header">
      <h3 class="card-title"><i class="fas fa-money-check-alt mr-1"></i>Payment Information</h3>
      <div class="card-tools">
        <a href="{{ route('payment-receive.index') }}" class="btn-cancel"><i class="fas fa-arrow-left mr-1"></i>Back</a>
        <a href="{{ route('payment-receive.edit', $payment->id) }}" class="btn-create ml-2"><i class="fas fa-edit mr-1"></i>Edit</a>
      </div>
    </div>

    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="font-weight-bold">Payment Type</label>
            <div>{{ ucfirst($payment->payment_type) }}</div>
          </div>

          <div class="form-group">
            <label class="font-weight-bold">Customer</label>
            <div>{{ $payment->customer->name ?? '-' }}</div>
          </div>

          <div class="form-group">
            <label class="font-weight-bold">Project</label>
            <div>{{ $payment->project->name ?? '-' }}</div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label class="font-weight-bold">Amount</label>
            <div>Rs. {{ number_format($payment->amount,2) }}</div>
          </div>

          <div class="form-group">
            <label class="font-weight-bold">Payment Date</label>
            <div>{{ $payment->payment_date }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection