@extends('admin.layouts.app')

@section('title', 'Invoice Details')

@section('content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mt-3">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">

                    <h1 class="m-0">
                        <i class="fas fa-file-invoice mr-2 text-primary"></i>
                        Invoice Details
                    </h1>

                    <a href="{{ route('invoice.index') }}"
                        class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </a>

                </div>
            </div>
        </div>
    </div>
</div>
<section class="content">
    <div class="container-fluid">

        <div class="justify-content-center">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-body p-5">

                    <!-- Invoice Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="font-weight-bold mb-1 text-primary">
                                INVOICE
                            </h2>

                            <p class="mb-0 text-muted">
                                Invoice #: {{ $invoice->id ?? 'N/A' }}
                            </p>
                        </div>

                        <div class="text-right">
                            <strong>Date:</strong><br>
                            {{ $invoice->invoice_date ?? now()->format('d/m/Y') }}
                        </div>
                    </div>

                    <hr>

                    <!-- Customer Details -->
                    <div class="row mb-4">
                        <div class="col-12 text-md-right">
                            <h5 class="font-weight-bold mb-3">
                                Bill To
                            </h5>

                            <p class="mb-1">
                                <strong>
                                    {{ $invoice->customer->name ?? '-' }}
                                </strong>
                            </p>

                            <p class="mb-1">
                                {{ $invoice->customer->address ?? 'No Address Available' }}
                            </p>

                            <p class="mb-0">
                                {{ $invoice->customer->mobile ?? '' }}
                            </p>
                        </div>
                    </div>

                    <!-- Invoice Summary -->
                    <h5 class="font-weight-bold mb-3">
                        Account Summary
                    </h5>

                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>Project</th>
                                    <th>Category</th>
                                    <th class="text-right">Amount</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>
                                        {{ $invoice->project->name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $invoice->subCategory->name ?? '-' }}
                                    </td>

                                    <td class="text-right">
                                        ₹ {{ number_format($invoice->amount ?? 0, 2) }}
                                    </td>
                                </tr>
                            </tbody>

                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-right">
                                        Total Amount
                                    </th>

                                    <th class="text-right text-success">
                                        ₹ {{ number_format($invoice->amount ?? 0, 2) }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Note -->
                    @if(!empty($invoice->note))
                        <div class="mb-4">
                            <label class="font-weight-bold">
                                Note:
                            </label>

                            <div class="border rounded p-3 bg-light">
                                {{ $invoice->note }}
                            </div>
                        </div>
                    @endif

        

                </div>
            </div>
        </div>

    </div>
</section>

@endsection