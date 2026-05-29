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

                <a href="{{ route('payment.edit', $payment->id) }}"
                    class="btn-create ml-2">
                    <i class="fas fa-edit mr-1"></i>
                    Edit
                </a>
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
                                #{{ $payment->id }}
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

                                <p class="mb-0">
                                    <strong>Project :</strong>
                                    {{ $payment->project->name ?? '-' }}
                                </p>
                            </div>
                        </div>

                        

                    </div>

                    <!-- Payment Summary Table -->
                    <h5 class="font-weight-bold">
                        Payment Information
                    </h5>

                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">

                            <thead class="bg-light">
                                <tr>
                                    <th>Vendor</th>
                                    <th>Project</th>
                                    <th class="text-right">
                                        Amount
                                    </th>
                                  
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>
                                        {{ $payment->vendor->name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $payment->project->name ?? '-' }}
                                    </td>

                                    <td class="text-right text-success font-weight-bold">
                                        ₹ {{ number_format($payment->amount, 2) }}
                                    </td>
                                </tr>
                            </tbody>

                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-right">
                                        Total Amount
                                    </th>

                                    <th colspan="2"
                                        class="text-center text-success">
                                        ₹ {{ number_format($payment->amount, 2) }}
                                    </th>
                                </tr>
                            </tfoot>

                        </table>
                    </div>

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