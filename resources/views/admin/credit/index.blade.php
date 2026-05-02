@extends('admin.layouts.app')
@section('title', 'Credits')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mt-3">
        <h1><i class="mr-2 text-teal"></i>Credits</h1>
    </div>
  </div>
</div>

<div class="pull-card">
  <div class="container-fluid" style="padding:0;">
    <div class="main-card table-card">
      <div class="main-card-head">
        <a href="{{ route('credit.create') }}" class="btn-create">
          <i class="fas fa-plus"></i> Add Credit
        </a>
      </div>

      <div class="main-card-body">
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
</div>
@endsection
