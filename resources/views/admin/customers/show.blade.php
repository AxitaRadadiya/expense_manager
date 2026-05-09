@extends('admin.layouts.app')
@section('title', 'Customer Details')

@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mt-3">
      <h1><i class="mr-2 text-teal"></i>{{ $customer->name }}</h1>
    </div>
  </div>
</div>

<div class="container-fluid-80">
  <div class="row">
    <div class="col-md-4">
      <div class="card card-outline card-primary shadow-sm">
        <div class="card-body text-center">
              <h4>{{ $customer->name }}</h4>
              <p>{{ $customer->email }}</p>
              <p>{{ $customer->mobile }}</p>
            </div>
      </div>
    </div>

    <div class="col-md-8">
      <div class="card card-outline card-primary shadow-sm">
        <div class="card-header">
          <h3 class="card-title">Customer Entries</h3>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table id="CustomerEntriesTable" class="table table-hover w-100">
              <thead>
                <tr>
                  <th>Sr</th>
                  <th>Category</th>
                  <th>Amount</th>
                  <th>Date</th>
                  <th>Project</th>
                </tr>
              </thead>
              <tbody>
                @foreach($customer->expenses()->limit(5)->get() as $idx => $expense)
                <tr>
                  <td>{{ $idx + 1 }}</td>
                  <td>{{ $expense->category }}</td>
                  <td>{{ $expense->amount }}</td>
                  <td>{{ $expense->created_at->format('Y-m-d') }}</td>
                  <td>{{ optional($expense->project)->name }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

@endsection