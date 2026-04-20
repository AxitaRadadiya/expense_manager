@extends('admin.layouts.app')
@section('title', 'Item Return Details')

@section('content')
<div class="content-header">
  <div class="container-fluid-80">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1><i class="mr-2 text-teal"></i>Item Return Details</h1>
      </div>
      <div class="col-sm-6 text-right">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('item-return.index') }}">Item Returns</a></li>
          <li class="breadcrumb-item active">View</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="container-fluid-80">
  <div class="card card-outline card-primary shadow-sm">
    <div class="card-header">
      <h3 class="card-title"><i class="fas fa-undo"></i> Return Information</h3>
      <div class="card-tools">
        <a href="{{ route('item-return.index') }}" class="btn-cancel"><i class="fas fa-arrow-left"></i> Back</a>
        <a href="{{ route('item-return.edit', $itemReturn->id) }}" class="btn-create ml-2"><i class="fas fa-edit"></i> Edit</a>
      </div>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group"><label class="font-weight-bold">Project</label><div>{{ $itemReturn->project->name ?? '-' }}</div></div>
          <div class="form-group"><label class="font-weight-bold">Item</label><div>{{ $itemReturn->item->name ?? '-' }}</div></div>
        </div>
        <div class="col-md-6">
          <div class="form-group"><label class="font-weight-bold">Date</label><div>{{ $itemReturn->date ? $itemReturn->date->format('Y-m-d') : '-' }}</div></div>
          <div class="form-group"><label class="font-weight-bold">Total Number</label><div>{{ $itemReturn->total_number ?? '-' }}</div></div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
