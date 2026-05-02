@extends('admin.layouts.app')
@section('title', 'Item Expense')

@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mt-3">
      <h1><i class="mr-2 text-teal"></i>Item Management</h1>
    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="card card-outline card-primary shadow-sm">
    <div class="card-body">
      <ul class="nav nav-pills mb-3">
        <li class="nav-item mr-2">
          <a class="nav-link @if(request()->routeIs('item-expense.*')) active @endif" href="{{ route('item-expense.index') }}">Item Expense</a>
        </li>
        <li class="nav-item">
          <a class="nav-link @if(request()->routeIs('item-return.*')) active @endif" href="{{ route('item-return.index') }}">Item Return</a>
        </li>
      </ul>

      <div class="d-flex justify-content-between align-items-center mb-3">
        <div></div>
        <a href="{{ route('item-expense.create') }}" class="btn-create">
          <i class="fas fa-plus"></i> Add Expense
        </a>
      </div>

      <div class="table-responsive">
        <table id="ItemExpenseTable" class="table table-hover w-100">
          <thead>
            <tr>
              <th>Sr No.</th>
              <th>Item</th>
              <th>Vendor</th>
              <th>Project</th>
              <th>Start Date</th>
              <th>End Date</th>
              <th>User</th>
              <th>Total Number</th>
              <th>Total Amount</th>
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