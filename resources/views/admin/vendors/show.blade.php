@extends('admin.layouts.app')
@section('title', 'Vendor Details')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1><i class="mr-2 text-teal"></i>Vendor Details</h1>
      </div>
      <div class="col-sm-6 text-right">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('vendor.index') }}">Vendors</a></li>
          <li class="breadcrumb-item active">View</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="card-tools text-right mr-3">
  <a href="{{ route('vendor.index') }}" class="btn-cancel"><i class="fas fa-arrow-left mr-1"></i>Back</a>
  @if(auth()->check() && auth()->user()->hasPermission('vendor-edit'))
  <a href="{{ route('vendor.edit', $vendor->id) }}" class="btn-create ml-2"><i class="fas fa-edit mr-1"></i>Edit</a>
  @endif
</div>
   

@php($addr = $vendor->relationLoaded('address') ? $vendor->address : $vendor->address()->first())
@php($bank = $vendor->relationLoaded('bankDetail') ? $vendor->bankDetail : $vendor->bankDetail()->first())

<!-- Vendor Statement Panel -->
<div class="container-fluid">
  <div class="vendor-statement mx-auto mt-3 mb-2" style="max-width:980px; background:#fff; padding:28px; border-radius:4px; box-shadow:0 0 0.5rem rgba(0,0,0,0.03);">
    <h3 class="text-center font-weight-bold mb-3">VENDOR STATEMENT</h3>

    <div class="row">
      <div class="col-md-6">
        <div class="mb-2"><strong>{{ $vendor->name }}</strong></div>
        <!-- <div class="text-muted small">{{ $vendor->company_name ?? '' }}</div> -->
        <div class="text-muted small">{{ $vendor->email ?? '' }}</div>
        <div class="text-muted small">{{ $vendor->mobile ?? '' }}</div>
        <div class="text-muted small">{{ $vendor->website ?? '' }}</div>
        
        
        <div class="mt-3 small"><strong>From:</strong></div>
        <div class="small">
          {{ optional($addr)->billing_street ?? '' }}<br>
          {{ optional($addr)->billing_city ?? '' }}, {{ optional($addr)->billing_state ?? '' }}<br>
          {{ optional($addr)->billing_country ?? '' }}<br>
        </div>
      </div>

      <div class="col-md-6 text-right">
        <div class="small text-muted">Date: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</div>
        <div class="mt-5 small"><strong>To:</strong></div>
        <div class="small">
          {{ $vendor->name }}<br>
          {{ optional($addr)->shipping_street ?? '' }}<br>
          {{ optional($addr)->shipping_city ?? '' }}, {{ optional($addr)->shipping_state ?? '' }}<br>
          {{ optional($addr)->shipping_country ?? '' }}<br>
        </div>
      </div>
    </div>

    <div class="mt-4">
      <h6><strong>Labour</strong></h6>
      <div class="table-responsive">
        <table class="table table-sm table-bordered mb-0">
          <thead class="thead-light"><tr><th>Project</th><th>Total Labour</th><th>Start Date</th><th>End Date</th><th>Amount</th></tr></thead>
          <tbody>
            @forelse($labourEntries ?? collect() as $entry)
            <tr>
              <td>{{ optional($entry->project)->name ?? '-' }}</td>
              <td>{{ $entry->total_labour ?? '-' }}</td>
              <td>{{ $entry->start_date ? $entry->start_date->format('d-m-Y') : '-' }}</td>
              <td>{{ $entry->end_date ? $entry->end_date->format('d-m-Y') : '-' }}</td>
              <td class="text-right">Rs. {{ number_format((float) $entry->amount, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="5">No Data Found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-3">
      <h6><strong>Items</strong></h6>
      <div class="table-responsive">
        <table class="table table-sm table-bordered mb-0">
          <thead class="thead-light"><tr><th>Item</th><th>Project</th><th>Total Number</th><th>Start Date</th><th>End Date</th><th>Amount</th></tr></thead>
          <tbody>
            @forelse($itemExpenses ?? collect() as $it)
            <tr>
              <td>{{ optional($it->item)->name ?? '-' }}</td>
              <td>{{ optional($it->project)->name ?? '-' }}</td>
              <td>{{ $it->total_number ?? '-' }}</td>
              <td>{{ $it->start_date ? $it->start_date->format('d-m-Y') : '-' }}</td>
              <td>{{ $it->end_date ? $it->end_date->format('d-m-Y') : '-' }}</td>
              <td class="text-right">Rs. {{ number_format((float) $it->total_amount, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="6">No Data Found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

@endsection