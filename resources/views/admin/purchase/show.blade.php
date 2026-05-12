@extends('admin.layouts.app')

@section('title', 'Purchase Details')

@section('content')

<!-- Header -->
<div class="content-header">
    <div class="container-fluid">
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

                <a href="{{ route('purchase.edit', $purchase->id) }}"
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
    <div class="container-fluid">

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
                        </div>

                    </div>

                    <hr>

                    <!-- Vendor & Project Info -->
                    <div class="row mb-4">

                        <div class="col-md-6">
                            <h5 class="font-weight-bold mb-3">
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
                            <h5 class="font-weight-bold mb-3">
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
                    <h5 class="font-weight-bold mb-3">
                        Purchase Summary
                    </h5>

                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">

                            <thead class="bg-light">
                                <tr>
                                    <th>Vendor</th>
                                    <th>Project</th>
                                    <th>Sub Category</th>
                                    <th class="text-center">
                                        Quantity
                                    </th>
                                    <th class="text-right">
                                        Amount
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>
                                        {{ $purchase->vendor->name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $purchase->project->name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $purchase->subCategory->name ?? '-' }}
                                    </td>

                                    <td class="text-center">
                                        {{ $purchase->quantity }}
                                    </td>

                                    <td class="text-right text-success font-weight-bold">
                                        ₹ {{ number_format($purchase->amount, 2) }}
                                    </td>
                                </tr>
                            </tbody>

                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-right">
                                        Total Amount
                                    </th>

                                    <th class="text-right text-success">
                                        ₹ {{ number_format($purchase->amount, 2) }}
                                    </th>
                                </tr>
                            </tfoot>

                        </table>
                    </div>

                    <!-- Note -->
                    @if(!empty($purchase->note))
                        <div class="mb-4">
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