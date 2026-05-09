@extends('admin.layouts.app')
@section('title', 'Purchases')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mt-3">
      <h1><i class="mr-2 text-teal"></i>Purchases</h1>
    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="card card-outline card-primary shadow-sm">
    <div class="card-body">
      <ul class="nav nav-pills mb-3">
        <li class="nav-item mr-2">
          <a class="nav-link @if(request()->routeIs('purchase.*')) active @endif" href="{{ route('purchase.index') }}">Purchases</a>
        </li>
        <li class="nav-item">
          <a class="nav-link @if(request()->routeIs('payment.*')) active @endif" href="{{ route('payment.index') }}">Payments Made</a>
        </li>
      </ul>

      <div class="d-flex justify-content-between align-items-center mb-3">
        <div></div>
        <a href="{{ route('purchase.create') }}" class="btn-create">
          <i class="fas fa-plus"></i> Add Purchase
        </a>
      </div>

      <div class="table-responsive">
        <table id="PurchasesTable" class="table table-hover w-100">
          <thead class="thead">
            <tr>
              <th>Sr No.</th>
              <th>Vendor</th>
              <th>Project</th>
              <th>Sub Category</th>
              <th>Amount</th>
              <th>Qty</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($purchases as $p)
              <tr>
                <td>{{ $p->id }}</td>
                <td>{{ $p->vendor->name ?? '-' }}</td>
                <td>{{ $p->project->name ?? '-' }}</td>
                <td>{{ $p->subCategory->name ?? '-' }}</td>
                <td>{{ number_format($p->amount,2) }}</td>
                <td>{{ $p->quantity }}</td>
                <td>{{ $p->purchase_date }}</td>
                <td>
                  <a href="{{ route('purchase.edit', $p->id) }}" class="btn btn-sm btn-primary">Edit</a>
                  <form method="POST" action="{{ route('purchase.destroy', $p->id) }}" style="display:inline">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Delete</button></form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  var addBtn = document.getElementById('purchaseAddBtn');
  function setButtonForActiveTab() {
    var active = document.querySelector('.nav-pills .nav-link.active');
    if (!active) return;
    if (active.getAttribute('href') === '#payments-tab') {
      addBtn.href = '{{ route('payment.create') }}';
      addBtn.innerHTML = '<i class="fas fa-plus"></i> Add Payment';
    } else {
      addBtn.href = '{{ route('purchase.create') }}';
      addBtn.innerHTML = '<i class="fas fa-plus"></i> Add Purchase';
    }
  }
  setButtonForActiveTab();
  $(document).on('shown.bs.tab', '.nav-pills a[data-toggle="tab"]', function (e) { setButtonForActiveTab(); });
});
</script>
@endpush
