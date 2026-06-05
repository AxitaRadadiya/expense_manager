@extends('admin.layouts.app')

@section('title', 'Invoice Details')

@section('content')

<div class="content-header">
    <div class="container-fluid-85">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">

                    <h1 class="m-0">
                        <i class="text-primary"></i>
                        Invoice Details
                    </h1>

                    <a href="{{ route('invoice.index') }}"
                        class="btn-cancel">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back
                    </a>

                </div>
            </div>
        </div>
    </div>
</div>
<section class="content">
    <div class="container-fluid-85">

        <div class="justify-content-center">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-body">

                    <!-- Invoice Header -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="font-weight-bold mb-1 text-primary">
                                INVOICE
                            </h2>

                            <p class="mb-0 text-muted">
                                Invoice #: {{ $invoice->id ?? 'N/A' }}
                            </p>
                            {{--
                            <p class="mb-0">
                                <strong>Status:</strong>
                                @if(!empty($invoice->status))
                                    {{ $invoice->status }}
                                @else
                                    Pending
                                @endif
                            </p>
                            --}}
                        </div>

                        <div class="text-right">
                            <strong>Date:</strong><br>
                            {{ $invoice->invoice_date ?? now()->format('d/m/Y') }}
                        </div>
                    </div>

                    <hr>

                    <!-- Customer Details -->
                    <div class="row mb-2">
                        <div class="col-12 text-md-right">
                            <h5 class="font-weight-bold">
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

                    <!-- Invoice Items -->
                    <h5 class="font-weight-bold">
                        Invoice Items
                    </h5>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>SR</th>
                                    <th>Item</th>
                                    <th>Expense Type</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-right">Unit Amount</th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($invoice->invoiceItems as $ii)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $ii->item->name ?? '-' }}</td>
                                    <td>{{ $ii->subCategory->name ?? '-' }}</td>
                                    <td class="text-center">{{ $ii->qty }}</td>
                                    <td class="text-right">₹ {{ number_format($ii->unit_amount, 2) }}</td>
                                    <td class="text-right">₹ {{ number_format($ii->total_amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No items found</td>
                                </tr>
                                @endforelse
                            </tbody>

                            <tfoot>
                                <tr>
                                    <th colspan="5" class="text-right">
                                        Total Amount
                                    </th>
                                    <th class="text-right text-success">
                                        ₹ {{ number_format($invoice->amount ?? 0, 2) }}
                                    </th>
                                </tr>
                                <tr>
                                    <th colspan="5" class="text-right">Due Amount</th>
                                    <th class="text-right text-danger">₹ {{ number_format($invoice->due_amount ?? 0, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Note -->
                    @if(!empty($invoice->note))
                        <div>
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