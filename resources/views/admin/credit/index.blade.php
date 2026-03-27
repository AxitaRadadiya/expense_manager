@extends('admin.layouts.app')

@section('title', 'Credits')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Credit List</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Credits</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-outline card-success shadow-sm">
            <div class="card-header d-flex align-items-center">
                <h3 class="card-title mb-0"><i class="fas fa-coins mr-2"></i>Project Credits</h3>
                <div class="card-tools ml-auto">
                    <a href="{{ route('credit.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus mr-1"></i>Add Credit
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table id="CreditTable" class="table table-bordered table-striped table-hover table-sm">
                    <thead class="thead-dark">
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
</section>
@endsection
