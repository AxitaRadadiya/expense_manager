@extends('admin.layouts.app')
@section('title', 'Add Item Expense')

@section('content')
<div class="content-header">
  <div class="container-fluid-80">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0"><i class="mr-2 text-teal"></i>Add Item Expense</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('item-expense.index') }}">Item Expenses</a></li>
                <li class="breadcrumb-item active">Create</li>  
            </ol>
        </div>
    </div>
  </div>
</div>

<div class="container-fluid-80">
  <div class="card card-outline card-primary shadow-sm">
    <div class="card-header">
      <h3 class="card-title"><i class="fas fa-plus mr-2"></i>New Item Expense</h3>
      <div class="card-tools">
        <a href="{{ route('item-expense.index') }}" class="btn-cancel"><i class="fas fa-arrow-left mr-1"></i>Back</a>
      </div>
    </div>

    <div class="card-body">
      <form action="{{ route('item-expense.store') }}" method="POST">
        @csrf

        <div class="form-row">
          <div class="form-group col-md-6">
            <label>Item <span class="text-danger">*</span></label>
            <select name="item_id" class="form-control select2" required>
              <option value="">Select Item</option>
              @foreach($items as $id => $name)
                <option value="{{ $id }}" {{ old('item_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
              @endforeach
            </select>
            @error('item_id')<span class="text-danger small">{{ $message }}</span>@enderror
          </div>

          <div class="form-group col-md-6">
            <label>Vendor <span class="text-danger">*</span></label>
            <select name="vendor_id" class="form-control select2" required>
              <option value="">Select Vendor</option>
              @foreach($vendors as $id => $name)
                <option value="{{ $id }}" {{ old('vendor_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
              @endforeach
            </select>
            @error('vendor_id')<span class="text-danger small">{{ $message }}</span>@enderror
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label>Project <span class="text-danger">*</span></label>
            <select name="project_id" class="form-control select2" required>
              <option value="">Select Project</option>
              @foreach($projects as $id => $name)
                <option value="{{ $id }}" {{ old('project_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
              @endforeach
            </select>
            @error('project_id')<span class="text-danger small">{{ $message }}</span>@enderror
          </div>

          <div class="form-group col-md-6">
            <label>User <span class="text-danger">*</span></label>
            <select name="user_id" class="form-control select2" required>
              <option value="">Select User</option>
              @foreach($users as $id => $name)
                <option value="{{ $id }}" {{ old('user_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
              @endforeach
            </select>
            @error('user_id')<span class="text-danger small">{{ $message }}</span>@enderror
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label>Start Date <span class="text-danger">*</span></label>
            <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" required>
            @error('start_date')<span class="text-danger small">{{ $message }}</span>@enderror
          </div>
          <div class="form-group col-md-6">
            <label>End Date <span class="text-danger">*</span></label>
            <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}" required>
            @error('end_date')<span class="text-danger small">{{ $message }}</span>@enderror
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label>Total Number</label>
            <input type="number" name="total_number" class="form-control" value="{{ old('total_number') }}">
            @error('total_number')<span class="text-danger small">{{ $message }}</span>@enderror
          </div>

          <div class="form-group col-md-6">
            <label>Total Amount <span class="text-danger">*</span></label>
            <input type="number" step="0.01" name="total_amount" class="form-control" value="{{ old('total_amount') }}" required>
            @error('total_amount')<span class="text-danger small">{{ $message }}</span>@enderror
          </div>
        </div>

        <div class="card-footer p-0 mt-3">
          <button type="submit" class="btn-submit"><i class="fas fa-save mr-1"></i>Create Expense</button>
          <a href="{{ route('item-expense.index') }}" class="btn-cancel ml-2"><i class="fas fa-times mr-1"></i>Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
