@extends('admin.layouts.app')
@section('title', 'Categories')
@section('content')

{{-- Content Header --}}
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Category List</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Categories</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
          <a href="#" class="btn btn-success waves-effect waves-light btn-sm float-right category-date-modal">Add Category</a>
          
        </h3>
      </div>
      <div class="card-body">
        <table id="CategoryTable" class="table dt-responsive nowrap">
          <thead>
            <tr>
              <th></th>
              <th>Sr No.</th>
              <th>Name</th>
              <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
            <tr>
              <th></th>
              <th>Sr No.</th>
              <th>Name</th>
              <th>Actions</th>
            </tr>
            </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal for adding Category -->
<div class="modal fade" id="CategoryModal" tabindex="-1" role="dialog" aria-labelledby="followUpPersonLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="followUpPersonLabel">Category</h5>
                <button type="button" class="close waves-effect waves-light" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('category.store') }}" method="POST" id="categoryForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="category_id" id="category_id">
                    <div class="row">
                        <div class="col-md-12">
                            <label>Name</label><span class="text-danger">*</span>
                            <input type="text" class="form-control" name="name" id="category_name" value="" required>
                            <span class="text-danger error category-name-error"></span> <!-- Error message will be inserted here -->
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary waves-effect waves-light" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary waves-effect waves-light" id="saveCategory">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection
