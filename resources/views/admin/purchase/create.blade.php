@extends('admin.layouts.app')
@section('title', 'Create Purchase')

@section('content')
<div class="content-header">
  <div class="container-fluid-80">
    <div class="row mb-2">
        <div class="col-sm-6"><h1 class="m-0">Create Purchase</h1></div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('purchase.index') }}">Purchases</a></li>
                <li class="breadcrumb-item active">Create</li>  
            </ol>
        </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid-80">
    <div class="card card-outline card-primary shadow-sm">
      <div class="card-body">
        <form action="{{ route('purchase.store') }}" method="POST">
          @csrf
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Vendor <span class="text-danger">*</span></label>
                <select name="vendor_id" class="form-control select2" required>
                  <option value="">-- Select Vendor --</option>
                  @foreach($vendors as $v)
                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label>Project <span class="text-danger">*</span></label>
                <select name="project_id" class="form-control select2" required>
                  <option value="">-- Select Project --</option>
                  @foreach($projects as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label>Sub Category <span class="text-danger">*</span></label>
                <select name="sub_category_id" class="form-control select2" required>
                  <option value="">-- Select Sub Category --</option>
                  @foreach($expenseSubCategories as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label>Amount <span class="text-danger">*</span></label>
                <input type="number" name="amount" step="0.01" min="0.01" class="form-control" required>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label>Quantity <span class="text-danger">*</span></label>
                <input type="number" name="quantity" min="1" class="form-control" required>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label>Purchase Date <span class="text-danger">*</span></label>
                <input type="date" name="purchase_date" class="form-control" min="{{ date('Y-m-d') }}" required>
              </div>
            </div>

            <div class="col-md-12">
              <div class="form-group">
                <label>Note <span class="text-danger">*</span></label>
                <textarea name="note" class="form-control" rows="3" required></textarea>
              </div>
            </div>
          </div>

          <div class="card-footer">
            <button class="btn-submit" type="submit">Save Purchase</button>
            <a href="{{ route('purchase.index') }}" class="btn-cancel ml-2">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

@endsection
