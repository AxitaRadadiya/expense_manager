@extends('admin.layouts.app')
@section('title', 'Transfers')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mt-3 mb-4">
        <h1><i class="mr-2 text-teal"></i>Transfers</h1>
    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="card card-outline card-primary shadow-sm">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">All Transfers</h5>
        <div></div>
        <a href="{{ route('transfer.create') }}" class="btn-create">
          <i class="fas fa-plus"></i> New Transfer
        </a>
      </div>

        <div class="table-responsive">
          <table id="TransferTable" class="table table-hover w-100">
            <thead class="thead">
              <tr>
                <th>Sr No.</th>
                <th>User</th>
                <th>Start Date</th>
                <th>Note</th>
                <th>Amount</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
    </div>
  </div>
</div>
@endsection
