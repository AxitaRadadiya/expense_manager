@extends('admin.layouts.app')

@section('title', 'Purchase Details')

@section('content')

<!-- Header -->
<div class="content-header">
    <div class="container-fluid-85">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">

                    <h1 class="m-0">
                        <i class="text-primary"></i>
                        Purchase Details
                    </h1>

                    <div>
                        @if(auth()->check() && auth()->user()->hasPermission('purchase-edit'))
                        <a href="{{ route('purchase.edit', $purchase->id) }}"
                            class="btn-submit ml-2">
                            <i class="fas fa-edit mr-1"></i>
                            Edit
                        </a>
                        @endif
                        <a href="{{ route('purchase.index') }}"
                            class="btn-cancel ml-2">
                            <i class="fas fa-arrow-left mr-1"></i>
                            Back
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<section class="content">
    <div class="container-fluid-85">

        <div class="justify-content-center">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-body">

                    <!-- Purchase Header -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="font-weight-bold mb-1 text-primary">
                                PURCHASE
                            </h2>

                            <p class="mb-0 text-muted">
                                Purchase ID: #{{ $purchase->id }}
                            </p>
                        </div>

                        <div class="text-right">
                            <strong>Date:</strong><br>
                            {{ $purchase->purchase_date }}
                        </div>
                    </div>

                    <hr>

                    <!-- Vendor & Project Info -->
                    <div class="row mb-3">
                        <div class="col-12 text-md-right">
                            <h5 class="font-weight-bold">
                                Vendor Details
                            </h5>

                            <p class="mb-1">
                                <strong>
                                    {{ $purchase->vendor->name ?? '-' }}
                                </strong>
                            </p>

                            <p class="mb-0">
                                <strong>Project:</strong>
                                {{ $purchase->project->name ?? '-' }}
                            </p>
                        </div>
                    </div>

                    <!-- Purchase Summary -->
                    <h5 class="font-weight-bold">
                        Purchase Summary
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
                                @forelse($purchase->purchaseItems as $pi)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $pi->item->name ?? '-' }}</td>
                                    <td>{{ $pi->subCategory->name ?? '-' }}</td>
                                    <td class="text-center">{{ $pi->quantity }}</td>
                                    <td class="text-right">₹ {{ number_format($pi->amount, 2) }}</td>
                                    <td class="text-right">₹ {{ number_format($pi->total_amount, 2) }}</td>
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
                                        ₹ {{ number_format($purchase->amount ?? 0, 2) }}
                                    </th>
                                </tr>
                                <tr>
                                    <th colspan="5" class="text-right">Due Amount</th>
                                    <th class="text-right text-danger">₹ {{ number_format($purchase->due_amount ?? 0, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Purchase Image -->
                    @if($purchase->image)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="font-weight-bold">Purchase Image</h5>
                            <div class="border rounded p-3 bg-light text-center">
                                <img src="{{ asset('storage/' . $purchase->image) }}" alt="Purchase Image" class="img-fluid rounded" style="max-height: 300px;">
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Note -->
                    @if(!empty($purchase->note))
                        <div class="mt-4">
                            <label class="font-weight-bold">
                                Note:
                            </label>

                            <div class="border rounded p-3 bg-light">
                                {{ $purchase->note }}
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>

    </div>
</section>

@endsection