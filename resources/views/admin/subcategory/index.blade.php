@extends('admin.layouts.app')
@section('title', 'Sub Categories')

@section('content')

<!-- <div class="content-header">
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
          <a class="nav-link @if(request()->routeIs('category.*')) active @endif" href="{{ route('category.index') }}">Category</a>
        </li>
        <li class="nav-item">
          <a class="nav-link @if(request()->routeIs('sub-category.*')) active @endif" href="{{ route('sub-category.index') }}">Sub Category</a>
        </li>
      </ul>

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
</div> -->

<!-- Add SubCategory Modal -->
<!-- <div class="modal fade" id="addSubCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addSubCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="{{ route('sub-category.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="addSubCategoryModalLabel">Add Sub Category</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Category</label>
            <select name="category_id" class="form-control" required>
              @foreach($categories as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>Sub Category Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn-submit">Save</button>
          <button type="button" class="btn-cancel" data-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div> -->

@endsection

@section('pageScript')
<!-- <script>
  $(function(){
    $('#SubCategoryTable').DataTable({
      paging: true, lengthChange: true, searching: true, ordering: true, info: true,
      autoWidth: false, responsive: true, processing: true, serverSide: true,
      ajax: { url: '{{ route("sub-category.list") }}', dataType: 'json', type: 'GET', data: { _token: '{{csrf_token()}}' } },
      columns: [ { data: 'id' }, { data: 'name' }, { data: 'category' }, { data: 'action', orderable: false, searchable: false } ],
      aoColumnDefs: [{ bSortable: false, aTargets: [-1] }]
    });
  });
</script> -->
@endsection