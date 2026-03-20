@extends('admin.layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Expense List</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Expenses</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <div class="row w-100 align-items-center">
          <div class="col">
            <h4 class="card-title mb-0">Expenses</h4>
          </div>
          <div class="col text-right">
            <a href="{{ route('expense.create') }}"
               class="btn btn-success btn-sm waves-effect waves-light">
              <i class="fa fa-plus mr-1"></i> Add Expense
            </a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <table id="ExpenseTable" class="table dt-responsive nowrap w-100">
          <thead>
            <tr>
              <th>Sr No.</th>
              <th>Project</th>
              <th>Expense Date</th>
              <th>Amount (₹)</th>
              <th>Payment Mode</th>
              <th>Status</th>
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