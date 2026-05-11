@extends('admin.layouts.app')
@section('title', 'Invoice Details')

@section('content')
<div class="content-header">
  <div class="container-fluid-80">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1><i class="mr-2 text-teal"></i>Invoice Details</h1>
      </div>
      <div class="col-sm-6 text-right">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('invoice.index') }}">Invoices</a></li>
          <li class="breadcrumb-item active">View</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="container-fluid-80">
  <div class="card card-outline card-primary shadow-sm">
    <div class="card-header">
      <h3 class="card-title"><i class="fas fa-file-invoice mr-1"></i>Invoice Information</h3>
      <div class="card-tools">
        <a href="{{ route('invoice.index') }}" class="btn-cancel"><i class="fas fa-arrow-left mr-1"></i>Back</a>
        <a href="{{ route('invoice.edit', $invoice->id) }}" class="btn-create ml-2"><i class="fas fa-edit mr-1"></i>Edit</a>
      </div>
    </div>

    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="font-weight-bold">Customer</label>
            <div>{{ $invoice->customer->name ?? '-' }}</div>
          </div>

          <div class="form-group">
            <label class="font-weight-bold">Project</label>
            <div>{{ $invoice->project->name ?? '-' }}</div>
          </div>

          <div class="form-group">
            <label class="font-weight-bold">Category</label>
            <div>{{ $invoice->subCategory->name ?? '-' }}</div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label class="font-weight-bold">Amount</label>
            <div>Rs. {{ number_format($invoice->amount,2) }}</div>
          </div>

          <div class="form-group">
            <label class="font-weight-bold">Invoice Date</label>
            <div>{{ $invoice->invoice_date }}</div>
          </div>
        </div>
      </div>

      @if(!empty($invoice->note))
      <div class="row mt-3">
        <div class="col-12">
          <div class="form-group">
            <label class="font-weight-bold">Note</label>
            <div>{{ $invoice->note }}</div>
          </div>
        </div>
      </div>
      @endif
    </div>
  </div>
</div>

@endsection