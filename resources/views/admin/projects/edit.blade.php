@extends('admin.layouts.app')
@section('title', 'Edit Project')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><i class="fas fa-edit mr-2 text-primary"></i>Edit Project</h1>
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
    <div class="card card-outline card-primary shadow-sm">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-folder-open mr-2"></i>Edit Project Details</h3>
        <div class="card-tools">
          <a href="{{ route('projects.index') }}" class="btn btn-default btn-sm">
            <i class="fas fa-arrow-left mr-1"></i>Back
          </a>
        </div>
      </div>
      <div class="card-body">
        <form action="{{ route('projects.update', $project) }}" method="POST">
          @csrf
          @method('PUT')

          <p class="text-uppercase text-muted font-weight-bold mb-3" style="font-size:.7rem;letter-spacing:1.4px;">
            <i class="fas fa-folder-open mr-1"></i> Project Info
          </p>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="font-weight-bold">Project Name <span class="text-danger">*</span></label>
                <input name="name" value="{{ old('name', $project->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label class="font-weight-bold">Start Date</label>
                <input type="date" name="start_date" value="{{ old('start_date', optional($project->start_date)->format('Y-m-d')) }}" class="form-control">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label class="font-weight-bold">End Date</label>
                <input type="date" name="end_date" value="{{ old('end_date', optional($project->end_date)->format('Y-m-d')) }}" class="form-control">
              </div>
            </div>
          </div>

          <hr>

          <p class="text-uppercase text-muted font-weight-bold mb-3" style="font-size:.7rem;letter-spacing:1.4px;">
            <i class="fas fa-users mr-1"></i> Assignment
          </p>

          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label class="font-weight-bold">Assigned Users</label>
                <select id="user_ids" name="user_ids[]" class="form-control select2 project-user-select @error('user_ids') is-invalid @enderror @error('user_ids.*') is-invalid @enderror" multiple data-placeholder="Select one or more users">
                  @php($selectedUserIds = old('user_ids', $project->users->pluck('id')->all()))
                  @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ in_array($user->id, $selectedUserIds) ? 'selected' : '' }}>{{ $user->name }}</option>
                  @endforeach
                </select>
                <small class="text-muted d-block mt-1">Update which users belong to this project.</small>
                @error('user_ids')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                @error('user_ids.*')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
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
        <button class="btn btn-primary"><i class="fas fa-save mr-1"></i>Save Changes</button>
        <a href="{{ route('projects.index') }}" class="btn btn-default ml-2">
          <i class="fas fa-times mr-1"></i>Cancel
        </a>
      </div>
        </form>
    </div>
  </div>
</section>
@endsection
