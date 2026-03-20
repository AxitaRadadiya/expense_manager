@extends('admin.layouts.app')
@section('title', 'Projects')
@section('content')

{{-- Content Header --}}
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Project List</h1>
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

{{-- Main Content --}}
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline">
                        <div class="card-header d-flex align-items-center">
                        <h3 class="card-title"><i class="fas fa-briefcase mr-2"></i>All Projects</h3>
                        <div class="card-tools ml-auto d-flex justify-content-end">
                            <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus mr-1"></i> New Project
                            </a>
                        </div>
                        </div>

                    <div class="card-body">
                        <table id="projectsTable" class="table table-bordered table-striped table-hover table-sm">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="40">#</th>
                                    <th>Project Name</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                    <th width="120" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
