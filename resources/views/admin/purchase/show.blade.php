@extends('admin.layouts.app')

@section('title', 'Purchase Details')

@section('content')

<!-- Header -->
<div class="content-header">
    <div class="container-fluid-85">
        <div class="row mt-3 align-items-center">

            <div class="col-md-6">
                <h1 class="m-0">
                    <i class=" mr-2 text-primary"></i>
                    Purchase Details
                </h1>
            </div>

            <div class="col-md-6 text-md-right mt-2 mt-md-0">
                <a href="{{ route('purchase.index') }}"
                    class="btn-cancel">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Back
                </a>

                @if(auth()->check() && auth()->user()->hasPermission('purchase-edit'))
                <a href="{{ route('purchase.edit', $purchase->id) }}"
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

                    <!-- Purchase Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">

                        <div>
                          

                            <p class="text-muted mb-0">
                                Purchase ID :
                                #{{ $purchase->id }}
                            </p>
                        </div>

                        <div class="text-right">
                            <strong>Purchase Date</strong><br>
                            {{ $purchase->purchase_date }}
                            
                            <div class="mt-1">
                                <strong>Due Amount:</strong>
                                <div class="text-danger font-weight-bold">₹ {{ number_format($purchase->due_amount ?? 0,2) }}</div>
                            </div>
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
                                    {{ $purchase->vendor->name ?? '-' }}
                                </p>

                                <p class="mb-0">
                                    <strong>Project :</strong>
                                    {{ $purchase->project->name ?? '-' }}
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5 class="font-weight-bold">
                                Product Details
                            </h5>

                            <div class="border rounded p-3 bg-light">
                                <p class="mb-1">
                                    <strong>Sub Category :</strong>
                                    {{ $purchase->subCategory->name ?? '-' }}
                                </p>

                                <p class="mb-0">
                                    <strong>Quantity :</strong>
                                    {{ $purchase->quantity }}
                                </p>
                            </div>
                        </div>

                    </div>

                    <!-- Amount Summary -->
                    <h5 class="font-weight-bold">
                        Purchase Summary
                    </h5>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Item</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-center">Days</th>
                                            <th class="text-right">Unit Amount</th>
                                            <th class="text-right">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($purchase->purchaseItems as $pi)
                                        <tr>
                                            <td>{{ $pi->item->name ?? '-' }}</td>
                                            <td class="text-center">{{ $pi->quantity }}</td>
                                            <td class="text-center">
                                                @if($pi->date_start && $pi->date_end)
                                                    {{ \Carbon\Carbon::parse($pi->date_start)->diffInDays(\Carbon\Carbon::parse($pi->date_end)) + 1 }}
                                                @else - @endif
                                            </td>
                                            <td class="text-right">₹ {{ number_format($pi->amount,2) }}</td>
                                            <td class="text-right">₹ {{ number_format($pi->total_amount,2) }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="5" class="text-center">No items</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Labour</th>
                                            <th class="text-center">Numbers</th>
                                            <th class="text-center">Days</th>
                                            <th class="text-right">Unit Amount</th>
                                            <th class="text-right">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($purchase->purchaseLabours as $pl)
                                        <tr>
                                            <td>{{ $pl->labour ?? '-' }}</td>
                                            <td class="text-center">{{ $pl->numbers }}</td>
                                            <td class="text-center">
                                                    @if($pl->date_start && $pl->date_end)
                                                        {{ \Carbon\Carbon::parse($pl->date_start)->diffInDays(\Carbon\Carbon::parse($pl->date_end)) + 1 }}
                                                @else - @endif
                                            </td>
                                            <td class="text-right">₹ {{ number_format($pl->amount,2) }}</td>
                                            <td class="text-right">₹ {{ number_format($pl->total_amount,2) }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="5" class="text-center">No labours</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mb-4">
                        <div class="text-right">
                            <div class="mb-1"><strong>Grand Total</strong></div>
                            <div class="text-success font-weight-bold" style="font-size:1.4rem">₹ {{ number_format($purchase->amount,2) }}</div>
                        </div>
                    </div>

                    <!-- Note -->
                    @if(!empty($purchase->note))
                        <div>
                            <label class="font-weight-bold">
                                Note
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