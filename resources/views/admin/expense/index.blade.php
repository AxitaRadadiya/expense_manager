@extends('admin.layouts.app')
@section('title', 'Expenses')

@section('content')
<div class="page-hero">
  <div class="orb"></div>
  <div class="container-fluid" style="position:relative;z-index:2;">
    <h1><i class="fas fa-receipt mr-2" style="color:rgba(255,255,255,.85);font-size:1.1rem;"></i>Expenses</h1>
    <p>Track project spending, payment modes, and debited entries in one place.</p>
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
