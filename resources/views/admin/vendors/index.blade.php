@extends('admin.layouts.app')
@section('title', 'Vendors')

@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mt-3">
      <h1><i class="mr-2 text-teal"></i>Vendors</h1>
    </div>
  </div>
</div>

<div class="pull-card">
  <div class="container-fluid" style="padding:0;">
    <div class="main-card table-card">

      <div class="main-card-head">
        <a href="{{ route('vendor.create') }}" class="btn-create">
          <i class="fas fa-plus"></i> Add Vendor
        </a>
      </div>

      <div class="main-card-body">
        <div class="table-responsive">
          <table id="VendorsTable" class="table table-hover w-100">
            <thead class="thead">
              <tr>
                <th>Sr No.</th>
                <th>Name</th>
                <th>Company</th>
                <th>Mobile</th>
                <th>Email</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>

@endsection