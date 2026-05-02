@extends('admin.layouts.app')
@section('title', 'Vendor Details')

@section('content')
<div class="content-header">
  <div class="container-fluid-80">
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

<div class="container-fluid-80">
  <div class="card card-outline card-primary shadow-sm">
    <div class="card-header">
      <h3 class="card-title"><i class="fas fa-user-tie mr-1"></i>Vendor Information</h3>
      <div class="card-tools">
        <a href="{{ route('vendor.index') }}" class="btn-cancel"><i class="fas fa-arrow-left mr-1"></i>Back</a>
        <a href="{{ route('vendor.edit', $vendor->id) }}" class="btn-create ml-2"><i class="fas fa-edit mr-1"></i>Edit</a>
      </div>
    </div>

    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="font-weight-bold">Name</label>
            <div>{{ $vendor->name }}</div>
          </div>

          <div class="form-group">
            <label class="font-weight-bold">Company</label>
            <div>{{ $vendor->company_name ?? '-' }}</div>
          </div>

          <div class="form-group">
            <label class="font-weight-bold">Mobile</label>
            <div>{{ $vendor->mobile }}</div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label class="font-weight-bold">Email</label>
            <div>{{ $vendor->email }}</div>
          </div>

          <div class="form-group">
            <label class="font-weight-bold">Address</label>
            <div>{{ $vendor->address ?? '-' }}</div>
          </div>

          <div class="form-group">
            <label class="font-weight-bold">Created At</label>
            <div>{{ $vendor->created_at ? $vendor->created_at->format('d M Y, H:i') : '-' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container-fluid-80 mt-3">
  <div class="card card-outline card-primary shadow-sm">
    <div class="card-header">
      <h3 class="card-title"><i class="fas fa-user-clock mr-1"></i> Labour List</h3>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table id="VendorLabourTable" class="table table-hover report-table mb-0">
          <thead>
            <tr>
              <th>Sr No.</th>
              <th>Project</th>
              <th>Total Labour</th>
              <th>Start Date</th>
              <th>End Date</th>
              <th>Amount</th>
            </tr>
          </thead>
          <tbody>
            @forelse($labourEntries ?? collect() as $i => $entry)
            <tr>
              <td>{{ $i + 1 }}</td>
              <td>{{ optional($entry->project)->name ?? '-' }}</td>
              <td>{{ $entry->total_labour ?? '-' }}</td>
              <td>{{ $entry->start_date ? $entry->start_date->format('d-m-Y') : '-' }}</td>
              <td>{{ $entry->end_date ? $entry->end_date->format('d-m-Y') : '-' }}</td>
              <td class="text-danger font-weight-bold">Rs. {{ number_format((float) $entry->amount, 2) }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="6" class="empty-table">No labour records found for this vendor.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="container-fluid-80 mt-3">
  <div class="card card-outline card-primary shadow-sm">
    <div class="card-header">
      <h3 class="card-title"><i class="fas fa-boxes mr-1"></i> Item List</h3>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table id="VendorItemTable" class="table table-hover report-table mb-0">
          <thead>
            <tr>
              <th>Sr No.</th>
              <th>Item Name</th>
              <th>Project</th>
              <th>User</th>
              <th>Start Date</th>
              <th>End Date</th>
              <th>Total Number</th>
              <th>Total Amount</th>
            </tr>
          </thead>
          <tbody>
            @forelse($itemExpenses ?? collect() as $i => $it)
            <tr>
              <td>{{ $i + 1 }}</td>
              <td>{{ optional($it->item)->name ?? '-' }}</td>
              <td>{{ optional($it->project)->name ?? '-' }}</td>
              <td>{{ optional($it->user)->name ?? '-' }}</td>
              <td>{{ $it->start_date ? $it->start_date->format('d-m-Y') : '-' }}</td>
              <td>{{ $it->end_date ? $it->end_date->format('d-m-Y') : '-' }}</td>
              <td>{{ $it->total_number ?? '-' }}</td>
              <td class="text-danger font-weight-bold">Rs. {{ number_format((float) $it->total_amount, 2) }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="8" class="empty-table">No item expense records found for this vendor.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

@endsection