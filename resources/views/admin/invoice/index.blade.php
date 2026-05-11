@extends('admin.layouts.app')
@section('title', 'Invoices')

@section('content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mt-3">
            <h1><i class="mr-2 text-teal"></i>Sales</h1>
        </div>
    </div>
</div>
 <ul class="nav nav-pills mb-3">
                <li class="nav-item mr-2">
                    <a class="nav-link @if(request()->routeIs('invoice.*')) active @endif" href="{{ route('invoice.index') }}">Invoices</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('payment-receive.*')) active @endif" href="{{ route('payment-receive.index') }}">Payments Received</a>
                </li>
            </ul>
<div class="container-fluid">
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">All Invoices</h5>
                <div></div>
                <a href="{{ route('invoice.create') }}" class="btn-create">
                    <i class="fas fa-plus"></i> Add Invoice
                </a>
            </div>

            <div class="table-responsive">
                <table id="InvoicesTable" class="table table-hover w-100">
                    <thead class="thead">
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Project</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Date</th>
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
