@extends('admin.layouts.app')
@section('title', 'Items')

@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mt-3 mb-4">
      <h1><i class="mr-2 text-teal"></i>Items</h1>
    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="card card-outline card-primary shadow-sm">
    <div class="card-body">

      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">All Items</h5>
        <div></div>
        <a href="#" class="btn-create item-modal">
          <i class="fas fa-plus"></i> Add Item
        </a>
      </div>

      <!-- Table -->
        <div class="table-responsive">
          <table id="ItemsTable" class="table table-hover w-100">
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

<!-- Modal -->
<div class="modal fade" id="ItemModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">

    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Item</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>

      <div class="modal-body">

        <form action="{{ route('item.store') }}" method="POST" id="itemForm">
          @csrf

          <input type="hidden" name="item_id" id="item_id">

          <div class="form-group mb-0">
            <label>Name <span class="text-danger">*</span></label>
            <input type="text"
                   class="form-control"
                   name="name"
                   id="item_name"
                   required>

            <span class="text-danger error item-name-error"></span>
          </div>

        </form>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn-cancel" data-dismiss="modal">Close</button>
        <button type="button" class="btn-submit" id="saveItem">Save</button>
      </div>

    </div>

  </div>
</div>
@endsection