
@extends('admin.layouts.app')
@section('title', 'Add Transfer')
@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><i class="fas fa-plus-circle mr-2 text-teal"></i>Add Transfer</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">Add Transfer</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6">
        <div class="card card-outline card-teal shadow-sm">
          <div class="card-header">
            <h3 class="card-title"><i class="fas fa-exchange-alt mr-2"></i>Transfer Details</h3>
            <div class="card-tools">
              <a href="{{ route('dashboard') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left mr-1"></i>Back
              </a>
            </div>
          </div>
          <div class="card-body">
            @if($errors->any())
              <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
              </div>
            @endif

            <form action="{{ route('transfer.store') }}" method="POST">
              @csrf
              <div class="form-group">
                <label class="font-weight-bold">User <span class="text-danger">*</span></label>
                <select name="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                  <option value="">— Select User —</option>
                  @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>
                      {{ $u->name }} ({{ $u->email }})
                    </option>
                  @endforeach
                </select>
                @error('user_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
              </div>

              <div class="form-group">
                <label class="font-weight-bold">Transfer Date</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                  </div>
                  <input type="date" name="start_date"
                         value="{{ old('start_date', now()->toDateString()) }}"
                         class="form-control @error('start_date') is-invalid @enderror">
                  @error('start_date')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
              </div>

              <div class="form-group">
                <label class="font-weight-bold">Amount <span class="text-danger">*</span></label>
                <div class="input-group">
                  <div class="input-group-prepend"><span class="input-group-text">₹</span></div>
                  <input name="amount" value="{{ old('amount','0.00') }}"
                         class="form-control @error('amount') is-invalid @enderror"
                         type="number" step="0.01" min="0" placeholder="0.00" required>
                  @error('amount')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
              </div>
              <div class="form-group">
                <label class="font-weight-bold">Note</label>
                <div class="input-group">
                  <textarea name="note" class="form-control" rows="3" placeholder="Optional note for this transfer">{{ old('note') }}</textarea>
                </div>
              </div>
              
          </div>
          <div class="card-footer">
            <button class="btn btn-teal"><i class="fas fa-save mr-1"></i>Create Transfer</button>
            <a href="{{ route('dashboard') }}" class="btn btn-default ml-2">
              <i class="fas fa-times mr-1"></i>Cancel
            </a>
          </div>
            </form>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection