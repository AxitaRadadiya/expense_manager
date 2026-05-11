@extends('admin.layouts.app')
@section('title', 'Payments Received')

@section('content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mt-3">
            <h1><i class="mr-2 text-teal"></i>Sales</h1>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-body">
            <ul class="nav nav-pills mb-3">
                <li class="nav-item mr-2">
                    <a class="nav-link @if(request()->routeIs('invoice.*')) active @endif" href="{{ route('invoice.index') }}">Invoices</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('payment-receive.*')) active @endif" href="{{ route('payment-receive.index') }}">Payments Received</a>
                </li>
            </ul>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div></div>
                <a href="{{ route('payment-receive.create') }}" class="btn-create">
                    <i class="fas fa-plus"></i> Add Payment
                </a>
            </div>

            <div class="table-responsive">
                <table id="PaymentsReceiveTable" class="table table-hover w-100">
                    <thead class="thead">
                        <tr>
                            <th>#</th>
                            <th>Type</th>
                            <th>Customer</th>
                            <th>Project</th>
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
