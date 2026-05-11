@extends('admin.layouts.app')
@section('title', 'Edit Invoice')

@section('content')
<div class="content-header">
    <div class="container-fluid-80">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="mr-2 text-teal"></i>Edit Invoice</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('invoice.index') }}">Invoices</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid-80">
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-pen mr-2"></i>Edit Invoice</h3>
            <div class="card-tools">
                <a href="{{ route('invoice.index') }}" class="btn-cancel"><i class="fas fa-arrow-left mr-1"></i>Back</a>
            </div>
        </div>
        <form action="{{ route('invoice.update', $invoice->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Customer <span class="text-danger">*</span></label>
                            <select name="customer_id" class="form-control select2" required>
                                <option value="">Select</option>
                                @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ old('customer_id', $invoice->customer_id)==$c->id? 'selected':'' }}>{{ $c->name }}</option>
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
                                <option value="{{ $p->id }}" {{ old('project_id', $invoice->project_id)==$p->id? 'selected':'' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                            @error('project_id')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Sub Category (Income) <span class="text-danger">*</span></label>
                            <select name="sub_category_id" class="form-control select2" required>
                                <option value="">Select</option>
                                @foreach($incomeSubCategories as $s)
                                <option value="{{ $s->id }}" {{ old('sub_category_id', $invoice->sub_category_id)==$s->id? 'selected':'' }}>{{ $s->name }}</option>
                                @endforeach
                            </select>
                            @error('sub_category_id')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Amount <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount', $invoice->amount) }}" required>
                            @error('amount')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Note <span class="text-danger">*</span></label>
                    <textarea name="note" class="form-control" required>{{ old('note', $invoice->note) }}</textarea>
                    @error('note')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Invoice Date <span class="text-danger">*</span></label>
                            <input type="date" name="invoice_date" class="form-control" min="{{ date('Y-m-d') }}" value="{{ old('invoice_date', $invoice->invoice_date) }}" required>
                            @error('invoice_date')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn-submit"><i class="fas fa-save mr-1"></i>Update Invoice</button>
                <a href="{{ route('invoice.index') }}" class="btn-cancel ml-2"><i class="fas fa-times mr-1"></i>Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection