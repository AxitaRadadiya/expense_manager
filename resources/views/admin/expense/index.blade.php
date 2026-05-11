@extends('admin.layouts.app')
@section('title', 'Expenses')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mt-3 mb-4">
        <h1><i class="mr-2 text-teal"></i>Expenses</h1>
    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="card card-outline card-primary shadow-sm">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">All Expenses</h5>
        <div></div>
        <a href="{{ route('expense.create') }}" class="btn-create">
          <i class="fas fa-plus"></i> Add Expense
        </a>
      </div>

        <div class="table-responsive">
          <table id="ExpenseTable" class="table table-hover w-100">
            <thead class="thead">
              <tr>
                <th>Sr No.</th>
                <th>Project</th>
                <th>Expense Date</th>
                <th>Amount (Rs)</th>
                <th>Payment Mode</th>
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
