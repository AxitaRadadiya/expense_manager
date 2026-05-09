@extends('admin.layouts.app')
@section('title', 'Categories')

@section('content')
<!-- <div class="content-header">
  <div class="container-fluid">
    <div class="row mt-3">
        <h1><i class="mr-2 text-teal"></i>Categories</h1>
    </div>
  </div>
</div>

<div class="pull-card">
  <div class="container-fluid" style="padding:0;">
    <div class="main-card table-card">
      <div class="main-card-head">
        <a href="#" class="btn-create category-date-modal">
          <i class="fas fa-plus"></i> Add Category
        </a>
      </div>

      <div class="main-card-body">
        <div class="table-responsive">
          <table id="CategoryTable" class="table table-hover w-100">
            <thead class="thead">
              <tr>
                <th></th>
                <th>Sr No.</th>
                <th>Name</th>
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

<div class="modal fade" id="CategoryModal" tabindex="-1" role="dialog" aria-labelledby="followUpPersonLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="followUpPersonLabel">Category</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="{{ route('category.store') }}" method="POST" id="categoryForm" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="category_id" id="category_id">
          <div class="form-group mb-0">
            <label>Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" id="category_name" value="" required>
            <span class="text-danger error category-name-error"></span>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-cancel" data-dismiss="modal">Close</button>
        <button type="button" class="btn-submit" id="saveCategory">Save</button>
      </div>
    </div>
  </div>
</div> -->
@endsection
