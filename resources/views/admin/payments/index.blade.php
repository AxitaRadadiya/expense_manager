@extends('admin.layouts.app')
@section('title', 'Payments')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mt-3">
      <h1><i class="mr-2 text-teal"></i>Purchases</h1>
    </div>
  </div>
</div>
<ul class="nav nav-pills mb-3">
        <li class="nav-item mr-2">
          <a class="nav-link @if(request()->routeIs('purchase.*')) active @endif" href="{{ route('purchase.index') }}">Purchases</a>
        </li>
        <li class="nav-item">
          <a class="nav-link @if(request()->routeIs('payment.*')) active @endif" href="{{ route('payment.index') }}">Payments Made</a>
        </li>
      </ul>
<div class="container-fluid">
  <div class="card card-outline card-primary shadow-sm">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">All Payments Made</h5>
        <div></div>
        <a href="{{ route('payment.create') }}" class="btn-create">
          <i class="fas fa-plus"></i> Add Payment
        </a>
      </div>

      <div class="table-responsive">
        <table id="PaymentsTable" class="table table-hover w-100">
          <thead class="thead">
            <tr>
              <th>Sr No.</th>
              <th>Vendor</th>
              <th>Project</th>
              <th>Amount</th>
              <th>Date</th>
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

