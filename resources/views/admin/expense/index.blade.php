@extends('admin.layouts.app')
@section('title', 'Expenses')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Expenses</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Projects</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="pull-card">
  <div class="container-fluid" style="padding:0;">
    <div class="main-card table-card">
      <div class="main-card-head">
        <div class="main-card-title">
          <i class="fas fa-list"></i> All Expenses
        </div>
        <a href="{{ route('expense.create') }}" class="btn-create">
          <i class="fas fa-plus"></i> Add Expense
        </a>
      </div>

      <div class="main-card-body">
        <div class="table-responsive">
          <table id="ExpenseTable" class="table table-hover w-100">
            <thead>
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
