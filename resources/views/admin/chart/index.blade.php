@extends('admin.layouts.app')
@section('title', 'Chart of Accounts')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mt-3">
        <h1><i class="mr-2 text-teal"></i>Chart of Accounts</h1>
    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="card card-outline card-primary shadow-sm">
    <div class="card-body">
      <ul class="nav nav-pills mb-3">
        <li class="nav-item mr-2">
          <a class="nav-link @if(request()->routeIs('category.*') || request()->routeIs('chart.index')) active @endif" href="#category-tab" data-toggle="tab">Category</a>
        </li>
        <li class="nav-item">
          <a class="nav-link @if(request()->routeIs('sub-category.*')) active @endif" href="#sub-category-tab" data-toggle="tab">Sub Category</a>
        </li>
      </ul>

      <div class="tab-content">
        <div class="tab-pane fade show active" id="category-tab">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div></div>
            <!-- <a href="#" class="btn-create category-date-modal">
              <i class="fas fa-plus"></i> Add Category
            </a> -->
          </div>

          <div class="table-responsive">
            <table id="CategoryTable" class="table table-hover w-100">
              <thead class="thead">
                <tr>
                  <th></th>
                  <th>Sr No.</th>
                  <th>Name</th>
                  <!-- <th>Actions</th> -->
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>

        <div class="tab-pane fade" id="sub-category-tab">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div></div>
            <a href="#addSubCategoryModal" data-toggle="modal" class="btn-create">
              <i class="fas fa-plus"></i> Add Sub Category
            </a>
          </div>

          <div class="table-responsive">
            <table id="SubCategoryTable" class="table table-hover w-100">
              <thead>
                <tr>
                  <th>Sr No.</th>
                  <th>Name</th>
                  <th>Category</th>
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
</div>

<!-- Category Modal -->
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
</div>

<!-- SubCategory Modal -->
<div class="modal fade" id="addSubCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addSubCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="subCategoryForm" action="{{ route('sub-category.store') }}" method="POST">
        @csrf
        <input type="hidden" name="subcategory_id" id="subcategory_id" value="">
        <div class="modal-header">
          <h5 class="modal-title" id="addSubCategoryModalLabel">Add Sub Category</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Category</label>
            <select name="category_id" id="sub_category_category_id" class="form-control" required>
              @foreach($categories as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>Sub Category Name</label>
            <input type="text" name="name" id="sub_category_name" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn-submit">Save</button>
          <button type="button" class="btn-cancel" data-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('pageScript')
<!-- SubCategory DataTable loads from global script -->
@endsection