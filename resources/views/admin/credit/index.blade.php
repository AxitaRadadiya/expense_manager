@extends('admin.layouts.app')
@section('title', 'Credits')

@section('content')
<div class="page-hero">
  <div class="orb"></div>
  <div class="container-fluid" style="position:relative;z-index:2;">
    <h1><i class="fas fa-coins mr-2" style="color:rgba(255,255,255,.85);font-size:1.1rem;"></i>Credits</h1>
    <p>Monitor incoming amounts, project credits, and finance activity with the same shared workspace.</p>
  </div>
</div>

<div class="pull-card">
  <div class="container-fluid" style="padding:0;">
    <div class="main-card table-card">
      <div class="main-card-head">
        <div class="main-card-title">
          <i class="fas fa-list"></i> Project Credits
        </div>
        <a href="{{ route('credit.create') }}" class="btn-create">
          <i class="fas fa-plus"></i> Add Credit
        </a>
      </div>

      <div class="main-card-body">
        <div class="table-responsive">
          <table id="CreditTable" class="table table-hover w-100">
            <thead>
              <tr>
                <th>Sr No.</th>
                <th>Project</th>
                <th>Credit Date</th>
                <th>Amount</th>
                <th>Created By</th>
                <th>Note</th>
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
