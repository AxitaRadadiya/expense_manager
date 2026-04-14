@extends('admin.layouts.app')
@section('title', 'Expenses')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mt-3">
        <h1><i class="mr-2 text-teal"></i>Expenses</h1>
    </div>
  </div>
</div>

<div class="pull-card">
  <div class="container-fluid" style="padding:0;">
    <div class="main-card table-card">
      <div class="main-card-head">
        <a href="{{ route('expense.create') }}" class="btn-create">
          <i class="fas fa-plus"></i> Add Expense
        </a>
      </div>

      <div class="main-card-body">
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
</div>
@endsection
