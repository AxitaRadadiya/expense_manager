@extends('admin.layouts.app')
@section('title', 'Credits')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Credits</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Projects</li>
                </ol>
            </div>
        </div>
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
