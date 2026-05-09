@extends('admin.layouts.app')
@section('title', 'Customers')

@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mt-3">
      <h1><i class="mr-2 text-teal"></i>Contacts</h1>
    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="card card-outline card-primary shadow-sm">
    <div class="card-body">
      <ul class="nav nav-pills mb-3">
        <li class="nav-item mr-2">
          <a class="nav-link @if(request()->routeIs('vendor.*')) active @endif" href="{{ route('vendor.index') }}">Vendors</a>
        </li>
        <li class="nav-item">
          <a class="nav-link @if(request()->routeIs('customer.*')) active @endif" href="{{ route('customer.index') }}">Customers</a>
        </li>
      </ul>

      <div class="d-flex justify-content-between align-items-center mb-3">
        <div></div>
        <a href="{{ route('customer.create') }}" class="btn-create">
          <i class="fas fa-plus"></i> Add Customer
        </a>
      </div>

      <div class="table-responsive">
        <table id="CustomersTable" class="table table-hover w-100">
          <thead class="thead">
            <tr>
              <th>Sr No.</th>
              <th>Name</th>
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

@endsection