@extends('admin.layouts.app')
@section('title', 'Transfers')

@section('content')
<div class="page-hero">
  <div class="orb"></div>
  <div class="container-fluid" style="position:relative;z-index:2;">
    <h1><i class="fas fa-exchange-alt mr-2" style="color:rgba(255,255,255,.85);font-size:1.1rem;"></i>Transfers</h1>
    <p>Manage internal fund movements and transfer records inside the same reusable model layout.</p>
  </div>
</div>

<div class="pull-card">
  <div class="container-fluid" style="padding:0;">
    <div class="main-card table-card">
      <div class="main-card-head">
        <div class="main-card-title">
          <i class="fas fa-list"></i> {{ ($canViewAllTransfers ?? false) ? 'All Transfers' : 'My Transfers' }}
        </div>
        <a href="{{ route('transfer.create') }}" class="btn-create">
          <i class="fas fa-plus"></i> New Transfer
        </a>
      </div>

      <div class="main-card-body">
        <div class="table-responsive">
          <table id="TransferTable" class="table table-hover w-100">
            <thead>
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
</div>
@endsection
