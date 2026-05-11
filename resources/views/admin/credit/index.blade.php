@extends('admin.layouts.app')
@section('title', 'Credits')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mt-3 mb-4">
        <h1><i class="mr-2 text-teal"></i>Credits</h1>
    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="card card-outline card-primary shadow-sm">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">All Credits</h5>
        <div></div>
        <a href="{{ route('credit.create') }}" class="btn-create">
          <i class="fas fa-plus"></i> Add Credit
        </a>
      </div>

        <div class="table-responsive">
          <table id="CreditTable" class="table table-hover w-100">
            <thead class="thead">
              <tr>
                <th>Sr No.</th>
                <th>Project</th>
                <th>Credit Date</th>
                <th>Amount</th>
                <th>Created By</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
  </div>
</div>
@endsection
