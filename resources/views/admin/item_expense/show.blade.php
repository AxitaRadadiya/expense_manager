@extends('admin.layouts.app')
@section('title', 'Item Expense Details')

@section('content')
<div class="content-header">
  <div class="container-fluid-80">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1><i class="mr-2 text-teal"></i>Item Expense Details</h1>
        </div>
        <div class="col-sm-6 text-right">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('item-expense.index') }}">Item Expenses</a></li>
                <li class="breadcrumb-item active">View</li>
            </ol>
        </div>
    </div>
  </div>
</div>

<div class="container-fluid-80">
  <div class="card card-outline card-primary shadow-sm">
    <div class="card-header">
      <h3 class="card-title"><i class="fas fa-money-bill-wave"></i> Expense Information</h3>
      <div class="card-tools">
        <a href="{{ route('item-expense.index') }}" class="btn-cancel"><i class="fas fa-arrow-left"></i> Back</a>
        <a href="{{ route('item-expense.edit', $itemExpense->id) }}" class="btn-create ml-2"><i class="fas fa-edit"></i> Edit</a>
      </div>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group"><label class="font-weight-bold">Item</label><div>{{ $itemExpense->item->name ?? '-' }}</div></div>
          <div class="form-group"><label class="font-weight-bold">Vendor</label><div>{{ $itemExpense->vendor->name ?? '-' }}</div></div>
          <div class="form-group"><label class="font-weight-bold">Project</label><div>{{ $itemExpense->project->name ?? '-' }}</div></div>
          <div class="form-group"><label class="font-weight-bold">User</label><div>{{ $itemExpense->user->name ?? '-' }}</div></div>
        </div>
        <div class="col-md-6">
          <div class="form-group"><label class="font-weight-bold">Start Date</label><div>{{ $itemExpense->start_date ? $itemExpense->start_date->format('Y-m-d') : '-' }}</div></div>
          <div class="form-group"><label class="font-weight-bold">End Date</label><div>{{ $itemExpense->end_date ? $itemExpense->end_date->format('Y-m-d') : '-' }}</div></div>
          <div class="form-group"><label class="font-weight-bold">Total Number</label><div>{{ $itemExpense->total_number ?? '-' }}</div></div>
          <div class="form-group"><label class="font-weight-bold">Total Amount</label><div>{{ number_format($itemExpense->total_amount, 2) }}</div></div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
