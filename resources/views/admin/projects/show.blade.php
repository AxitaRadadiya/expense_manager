@extends('admin.layouts.app')
@section('title', 'Project Details')

@section('content')
@php
$statusClass = match($project->status) {
'active' => 'success',
'completed' => 'info',
'pending' => 'warning',
default => 'secondary',
};
@endphp

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Project View</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projects</a></li>
          <li class="breadcrumb-item active">{{ $project->name }}</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="card border-0 shadow-sm">
      <div class="card-body p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
          <div class="mb-3 mb-md-0">
            <div class="d-flex align-items-center mb-2">
              <div class="rounded-circle d-inline-flex align-items-center justify-content-center mr-3"
                   style="width:56px;height:56px;background:rgba(0,141,141,.12);color:#339D9D;">
                <i class="fas fa-briefcase"></i>
              </div>
              <div>
                <h2 class="h4 mb-1 font-weight-bold">{{ $project->name }}</h2>
                <span class="badge badge-{{ $statusClass }}">{{ ucfirst($project->status ?? 'pending') }}</span>
              </div>
            </div>
            <p class="text-muted mb-0">Simple overview of project details and assigned users.</p>
          </div>

          <div class="d-flex">
            <a href="{{ route('projects.index') }}" class="btn-cancel mr-2">
              <i class="fas fa-arrow-left mr-1"></i>Back
            </a>
            <a href="{{ route('projects.edit', $project) }}" class="btn-submit">
              <i class="fas fa-edit mr-1"></i>Edit
            </a>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-8">
            <div class="border rounded p-3 bg-light">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <div class="text-muted small mb-1">Start Date</div>
                  <div class="font-weight-bold">{{ optional($project->start_date)->format('d-m-Y') ?? '-' }}</div>
                </div>
                <div class="col-md-6 mb-3">
                  <div class="text-muted small mb-1">End Date</div>
                  <div class="font-weight-bold">{{ optional($project->end_date)->format('d-m-Y') ?? '-' }}</div>
                </div>
                <div class="col-md-6 mb-3">
                  <div class="text-muted small mb-1">Budget</div>
                  <div class="font-weight-bold text-success">Rs. {{ number_format((float) ($project->amount ?? 0), 2) }}</div>
                </div>
                <div class="col-md-6 mb-3">
                  <div class="text-muted small mb-1">Assigned Users</div>
                  <div class="font-weight-bold">{{ $project->users->count() }}</div>
                </div>
                <div class="col-md-12">
                  <div class="text-muted small mb-2">Project Note</div>
                  <div class="mb-0">{{ filled($project->note) ? $project->note : 'No note added for this project.' }}</div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 mt-4 mt-lg-0">
            <div class="border rounded p-3 h-100 bg-light">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="h6 mb-0 font-weight-bold">Assigned User List</h3>
                <span class="badge badge-info">{{ $project->users->count() }}</span>
              </div>

              @if($project->users->isEmpty())
              <div class="text-muted">No users assigned yet.</div>
              @else
              @foreach($project->users as $user)
              <div class="border rounded bg-white px-3 py-2 mb-2">
                <div class="font-weight-bold">{{ $user->name }}</div>
                <div class="small text-muted">{{ $user->email }}</div>
                <div class="small mt-1">
                  <span>{{ optional($user->role)->name ?? 'No Role' }}</span>
                  <span class="mx-1">|</span>
                  <span>{{ $user->mobile ?? 'No Mobile' }}</span>
                </div>
              </div>
              @endforeach
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection