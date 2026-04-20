@extends('admin.layouts.app')
@section('title', 'Vendor Details')

@section('content')
<div class="content-header">
  <div class="container-fluid-80">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1><i class="mr-2 text-teal"></i>Vendor Details</h1>
        </div>
        <div class="col-sm-6 text-right">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('vendor.index') }}">Vendors</a></li>
                <li class="breadcrumb-item active">View</li>
            </ol>
        </div>
    </div>
  </div>
</div>

<div class="container-fluid-80">
  <div class="card card-outline card-primary shadow-sm">
    <div class="card-header">
      <h3 class="card-title"><i class="fas fa-user-tie mr-1"></i>Vendor Information</h3>
      <div class="card-tools">
        <a href="{{ route('vendor.index') }}" class="btn-cancel"><i class="fas fa-arrow-left mr-1"></i>Back</a>
        <a href="{{ route('vendor.edit', $vendor->id) }}" class="btn-create ml-2"><i class="fas fa-edit mr-1"></i>Edit</a>
      </div>
    </div>

    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="font-weight-bold">Name</label>
            <div>{{ $vendor->name }}</div>
          </div>

          <div class="form-group">
            <label class="font-weight-bold">Company</label>
            <div>{{ $vendor->company_name ?? '-' }}</div>
          </div>

          <div class="form-group">
            <label class="font-weight-bold">Mobile</label>
            <div>{{ $vendor->mobile }}</div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label class="font-weight-bold">Email</label>
            <div>{{ $vendor->email }}</div>
          </div>

          <div class="form-group">
            <label class="font-weight-bold">Address</label>
            <div>{{ $vendor->address ?? '-' }}</div>
          </div>

          <div class="form-group">
            <label class="font-weight-bold">Created At</label>
            <div>{{ $vendor->created_at ? $vendor->created_at->format('d M Y, H:i') : '-' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
