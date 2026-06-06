@extends('admin.layouts.app')

@section('title', 'Payment Details')

@section('content')

<!-- Header -->
<div class="content-header">
    <div class="container-fluid-85">

        <div class="row mt-3 align-items-center">

            <div class="col-md-6">
                <h1 class="m-0">
                    <i class="mr-2 text-primary"></i>
                    Payment Details
                </h1>
            </div>

            <div class="col-md-6 text-md-right mt-2 mt-md-0">
                <a href="{{ route('payment.index') }}"
                    class="btn-cancel">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Back
                </a>

                @if(auth()->check() && auth()->user()->hasPermission('purchase-edit'))
                <a href="{{ route('payment.edit', $payment->id) }}"
                    class="btn-submit ml-2">
                    <i class="fas fa-edit mr-1"></i>
                    Edit
                </a>
                @endif
            </div>

        </div>
    </div>
</div>

<!-- Main Content -->
<section class="content">
    <div class="container-fluid-85">

        <div class="justify-content-center">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-body p-4">

                    <!-- Payment Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">

                        <div>
                            <p class="text-muted mb-0">
                                Payment ID :
                                {{ $payment->id }}
                            </p>
                        </div>

                        <div class="text-right">
                            <strong>Payment Date</strong><br>
                            {{ $payment->payment_date }}
                        </div>

                    </div>

                    <hr>

                    <!-- Vendor & Project Info -->
                    <div class="row mb-4">

                        <div class="col-md-6">
                            <h5 class="font-weight-bold">
                                Vendor Details
                            </h5>

                            <div class="border rounded p-3 bg-light">
                                <p class="mb-1">
                                    <strong>Vendor :</strong>
                                    {{ $payment->vendor->name ?? '-' }}
                                </p>
                            </div>
                        </div>

                        

                    </div>

                    <!-- Purchase Allocations -->
                    @if($payment->allocations && $payment->allocations->count() > 0)
                        <h5 class="font-weight-bold mb-3">
                            Purchase Invoice Allocations
                        </h5>

                        <div class="table-responsive mb-4">
                            <table class="table table-bordered table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Purchase No</th>
                                        <th>Project</th>
                                        <th class="text-right">Purchase Amount</th>
                                        <th class="text-right">Allocated Amount</th>
                                        <th class="text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $totalAllocated = 0; @endphp
                                    @foreach($payment->allocations as $allocation)
                                        @php $totalAllocated += $allocation->amount; @endphp
                                        <tr>
                                            <td>{{ $allocation->purchase->purchase_date ?? '-' }}</td>
                                            <td>{{ $allocation->purchase->id }}</td>
                                            <td>{{ $allocation->purchase->project->name ?? '-' }}</td>
                                            <td class="text-right">₹ {{ number_format($allocation->purchase->amount ?? 0, 2) }}</td>
                                            <td class="text-right text-primary font-weight-bold">₹ {{ number_format($allocation->amount, 2) }}</td>
                                            <td class="text-right">
                                                @if($allocation->purchase->status === 'paid')
                                                    <span class="badge badge-success">Paid</span>
                                                @else
                                                    <span class="badge badge-warning">Pending</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-right">Total Allocated</th>
                                        <th class="text-center text-primary font-weight-bold">₹ {{ number_format($totalAllocated, 2) }}</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info mb-4" role="alert">
                            <i class="fas fa-info-circle mr-2"></i>
                            No purchase invoices allocated to this payment.
                        </div>
                    @endif

                    <!-- Note -->
                    @if(!empty($payment->note))
                        <div class="mb-4">
                            <label class="font-weight-bold">
                                Note
                            </label>

                            <div class="border rounded p-3 bg-light">
                                {{ $payment->note }}
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>

    </div>
</section>

@endsection