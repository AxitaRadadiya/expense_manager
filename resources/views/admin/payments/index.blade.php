@extends('admin.layouts.app')
@section('title', 'Payments')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mt-3">
      <h1><i class="mr-2 text-teal"></i>Payments</h1>
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
        <a href="{{ route('payment.create') }}" class="btn-create">
          <i class="fas fa-plus"></i> Add Payment
        </a>
      </div>

      <div class="table-responsive">
        <table id="PaymentsTable" class="table table-hover w-100">
          <thead class="thead">
            <tr>
              <th>Sr No.</th>
              <th>Vendor</th>
              <th>Project</th>
              <th>Amount</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($payments as $p)
              <tr>
                <td>{{ $p->id }}</td>
                <td>{{ $p->vendor->name ?? '-' }}</td>
                <td>{{ $p->project->name ?? '-' }}</td>
                <td>{{ number_format($p->amount,2) }}</td>
                <td>{{ $p->payment_date }}</td>
                <td>
                  <a href="{{ route('payment.edit', $p->id) }}" class="btn btn-sm btn-primary">Edit</a>
                  <form method="POST" action="{{ route('payment.destroy', $p->id) }}" style="display:inline">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Delete</button></form>
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

