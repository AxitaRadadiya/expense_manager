{{-- ============================================================
     PROJECT EDIT  →  resources/views/admin/projects/edit.blade.php
     ============================================================ --}}
@extends('admin.layouts.app')
@section('title', 'Edit Project')
@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><i class="fas fa-edit mr-2 text-teal"></i>Edit Project</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projects</a></li>
          <li class="breadcrumb-item active">Edit</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="card card-outline card-teal shadow-sm">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-folder-open mr-2"></i>{{ $project->name }}</h3>
        <div class="card-tools">
          <a href="{{ route('projects.index') }}" class="btn btn-default btn-sm">
            <i class="fas fa-arrow-left mr-1"></i>Back
          </a>
        </div>
      </div>
      <div class="card-body">
        @if($errors->any())
          <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <h5><i class="icon fas fa-ban"></i> Error</h5>
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
          </div>
        @endif

        <form action="{{ route('projects.update', $project) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="font-weight-bold">Project Name <span class="text-danger">*</span></label>
                <input name="name" value="{{ old('name', $project->name) }}"
                       class="form-control @error('name') is-invalid @enderror" required>
                @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label class="font-weight-bold">Start Date</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                  </div>
                  <input type="date" name="start_date"
                         value="{{ old('start_date', optional($project->start_date)->format('Y-m-d')) }}"
                         class="form-control">
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label class="font-weight-bold">End Date</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                  </div>
                  <input type="date" name="end_date"
                         value="{{ old('end_date', optional($project->end_date)->format('Y-m-d')) }}"
                         class="form-control">
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="font-weight-bold">Note</label>
                <textarea name="note" class="form-control" rows="3">{{ old('note', $project->note) }}</textarea>
              </div>
            </div>
          </div>
      </div>
      <div class="card-footer">
        <button class="btn btn-teal"><i class="fas fa-save mr-1"></i>Save Changes</button>
        <a href="{{ route('projects.index') }}" class="btn btn-default ml-2">
          <i class="fas fa-times mr-1"></i>Cancel
        </a>
      </div>
        </form>
    </div>
  </div>
</section>
@endsection