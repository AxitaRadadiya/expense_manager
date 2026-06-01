@extends('admin.layouts.app')
@section('title', 'Customer Details')

@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1><i class="mr-2 text-teal"></i>Customer Details</h1>
      </div>
      <div class="col-sm-6 text-right">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('customer.index') }}">Customers</a></li>
          <li class="breadcrumb-item active">View</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="card-tools text-right mr-3">
  <a href="{{ route('customer.index') }}" class="btn-cancel"><i class="fas fa-arrow-left mr-1"></i>Back</a>
<<<<<<< HEAD
  @if(auth()->check() && auth()->user()->hasPermission('customer-edit'))
  <a href="{{ route('customer.edit', $customer->id) }}" class="btn-create ml-2"><i class="fas fa-edit mr-1"></i>Edit</a>
  @endif
=======
  <a href="{{ route('customer.edit', $customer->id) }}" class="btn-submit ml-2"><i class="fas fa-edit mr-1"></i>Edit</a>
>>>>>>> uichangecard
</div>

@php($addr = $customer->relationLoaded('address') ? $customer->address : $customer->address()->first())
@php($bank = $customer->relationLoaded('bankDetail') ? $customer->bankDetail : $customer->bankDetail()->first())

<!-- Customer Statement Panel -->
<div class="container-fluid">
  <div class="customer-statement mx-auto my-4" style="max-width:980px; background:#fff; padding:28px; border-radius:4px; box-shadow:0 0 0.5rem rgba(0,0,0,0.03);">
    <h3 class="text-center font-weight-bold mb-3">CUSTOMER STATEMENT</h3>

    <div class="row">
      <div class="col-md-6">
        <div class="mb-2"><strong>{{ $customer->name }}</strong></div>
        <div class="text-muted small">{{ $customer->email ?? '' }}</div>
        <div class="text-muted small">{{ $customer->mobile ?? '' }}</div>
        <div class="text-muted small">{{ $customer->website ?? '' }}</div>
        <div class="mt-3 small"><strong>From:</strong></div>
        <div class="small">
          {{ optional($addr)->billing_street ?? '' }}<br>
          {{ optional($addr)->billing_city ?? '' }}, {{ optional($addr)->billing_state ?? '' }}<br>
          {{ optional($addr)->billing_country ?? '' }}<br>
        </div>
      </div>

      <div class="col-md-6 text-right">
        <div class="small text-muted">Date: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</div>
        <div class="text-muted small mt-2">
        GST No: {{ $customer->gst_number ?? '-' }} <br> 
        PAN No: {{ $customer->pan_number ?? '-' }}
        </div>
        <div class="mt-3 small"><strong>To:</strong></div>
        <div class="small">
          {{ $customer->name }}<br>
          {{ optional($addr)->shipping_street ?? '' }}<br>
          {{ optional($addr)->shipping_city ?? '' }}, {{ optional($addr)->shipping_state ?? '' }}<br>
          {{ optional($addr)->shipping_country ?? '' }}<br>
        </div>
      </div>
    </div>

    <div class="mt-4">
      <h6><strong>Customer Entries</strong></h6>
      <div class="table-responsive">
        <table class="table table-sm table-bordered mb-0">
          <thead class="thead-light"><tr><th>Category</th><th>Amount</th><th>Date</th><th>Project</th></tr></thead>
          <tbody>
            @forelse($customer->expenses()->limit(5)->get() as $expense)
            <tr>
              <td>{{ $expense->category }}</td>
              <td>{{ $expense->amount }}</td>
              <td>{{ $expense->created_at->format('Y-m-d') }}</td>
              <td>{{ optional($expense->project)->name }}</td>
            </tr>
            @empty
            <tr><td colspan="4">No Data Found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

@endsection