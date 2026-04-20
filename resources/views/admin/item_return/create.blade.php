@extends('admin.layouts.app')
@section('title', 'Add Item Return')

@section('content')
<div class="content-header">
  <div class="container-fluid-80">
    <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0"><i class="mr-2 text-teal"></i>Add Item Return</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ route('item-return.index') }}">Item Returns</a></li>
              <li class="breadcrumb-item active">Create</li>  
          </ol>
        </div>
    </div>
  </div>
</div>

<div class="container-fluid-80">
  <div class="card card-outline card-primary shadow-sm">
    <div class="card-header">
      <h3 class="card-title"><i class="fas fa-plus mr-2"></i>New Item Return</h3>
      <div class="card-tools">
        <a href="{{ route('item-return.index') }}" class="btn-cancel"><i class="fas fa-arrow-left mr-1"></i>Back</a>
      </div>
    </div>

    <div class="card-body">
      <form action="{{ route('item-return.store') }}" method="POST">
        @csrf

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
            <label>Item <span class="text-danger">*</span></label>
            <select name="item_id" class="form-control select2" required>
              <option value="">Select Item</option>
              @foreach($items as $id => $name)
                <option value="{{ $id }}" {{ old('item_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
              @endforeach
            </select>
            @error('item_id')<span class="text-danger small">{{ $message }}</span>@enderror
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label>Date <span class="text-danger">*</span></label>
            <input type="date" name="date" class="form-control" value="{{ old('date') }}" required>
            @error('date')<span class="text-danger small">{{ $message }}</span>@enderror
          </div>

          <div class="form-group col-md-6">
            <label>Total Number</label>
            <input type="number" name="total_number" class="form-control" value="{{ old('total_number') }}">
            @error('total_number')<span class="text-danger small">{{ $message }}</span>@enderror
          </div>
        </div>

        <div class="card-footer p-0 mt-3">
          <button type="submit" class="btn-submit"><i class="fas fa-save mr-1"></i>Create Return</button>
          <a href="{{ route('item-return.index') }}" class="btn-cancel ml-2"><i class="fas fa-times mr-1"></i>Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
