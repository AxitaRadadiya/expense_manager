@extends('admin.layouts.app')
@section('title', 'Record Payment')

@section('content')

<div class="content-header">
    <div class="container-fluid-80">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="mr-2 text-teal"></i>Record Payment</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('payment-receive.index') }}">Payments Received</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid-80">
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-money-check-alt mr-2"></i>New Payment</h3>
            <div class="card-tools">
                <a href="{{ route('payment-receive.index') }}" class="btn-cancel"><i class="fas fa-arrow-left mr-1"></i>Back</a>
            </div>
        </div>
        <form action="{{ route('payment-receive.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Payment Type <span class="text-danger">*</span></label>
                            <select name="payment_type" class="form-control select2" required>
                                <option value="cash" {{ old('payment_type')=='cash'?'selected':'' }}>Cash</option>
                                <option value="online" {{ old('payment_type')=='online'?'selected':'' }}>Online</option>
                                <option value="cheque" {{ old('payment_type')=='cheque'?'selected':'' }}>Cheque</option>
                            </select>
                            @error('payment_type')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Customer (optional)</label>
                            <select name="customer_id" class="form-control select2">
                                <option value="">Select</option>
                                @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ old('customer_id')==$c->id? 'selected':'' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                            @error('customer_id')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Project <span class="text-danger">*</span></label>
                            <select name="project_id" class="form-control select2" required>
                                <option value="">Select</option>
                                @foreach($projects as $p)
                                <option value="{{ $p->id }}" {{ old('project_id')==$p->id? 'selected':'' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                            @error('project_id')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Amount <span class="text-danger">*</span></label>
                            <input type="number" name="amount" step="0.01" class="form-control" value="{{ old('amount') }}" required>
                            @error('amount')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Payment Date <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" class="form-control" min="{{ date('Y-m-d') }}" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                            @error('payment_date')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn-submit"><i class="fas fa-save mr-1"></i>Save Payment</button>
                <a href="{{ route('payment-receive.index') }}" class="btn-cancel ml-2"><i class="fas fa-times mr-1"></i>Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection