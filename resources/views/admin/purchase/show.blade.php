@extends('admin.layouts.app')
@section('title', 'Purchase Details')

@section('content')
<div class="content-header">
  <div class="container-fluid-80">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1><i class="mr-2 text-teal"></i>Purchase Details</h1>
      </div>
      <div class="col-sm-6 text-right">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('purchase.index') }}">Purchases</a></li>
          <li class="breadcrumb-item active">View</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="container-fluid-80">
  <div class="card card-outline card-primary shadow-sm">
    <div class="card-header">
      <h3 class="card-title"><i class="fas fa-shopping-cart mr-1"></i>Purchase Information</h3>
      <div class="card-tools">
        <a href="{{ route('purchase.index') }}" class="btn-cancel"><i class="fas fa-arrow-left mr-1"></i>Back</a>
        <a href="{{ route('purchase.edit', $purchase->id) }}" class="btn-create ml-2"><i class="fas fa-edit mr-1"></i>Edit</a>
      </div>
    </div>

    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="font-weight-bold">Vendor</label>
            <div>{{ $purchase->vendor->name ?? '-' }}</div>
          </div>

          <div class="form-group">
            <label class="font-weight-bold">Project</label>
            <div>{{ $purchase->project->name ?? '-' }}</div>
          </div>

          <div class="form-group">
            <label class="font-weight-bold">Sub Category</label>
            <div>{{ $purchase->subCategory->name ?? '-' }}</div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label class="font-weight-bold">Amount</label>
            <div>Rs. {{ number_format($purchase->amount,2) }}</div>
          </div>

          <div class="form-group">
            <label class="font-weight-bold">Quantity</label>
            <div>{{ $purchase->quantity }}</div>
          </div>

          <div class="form-group">
            <label class="font-weight-bold">Purchase Date</label>
            <div>{{ $purchase->purchase_date }}</div>
          </div>
        </div>
      </div>

      @if(!empty($purchase->note))
      <div class="row mt-3">
        <div class="col-12">
          <div class="form-group">
            <label class="font-weight-bold">Note</label>
            <div>{{ $purchase->note }}</div>
          </div>
        </div>
      </div>
      @endif
    </div>
  </div>
</div>

@endsection
