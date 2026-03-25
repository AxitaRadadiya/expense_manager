{{-- ============================================================
     PROJECT SHOW  →  resources/views/admin/projects/show.blade.php
     ============================================================ --}}
@extends('admin.layouts.app')
@section('title', 'Project Details')
@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><i class="fas fa-folder-open mr-2 text-teal"></i>{{ $project->name }}</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projects</a></li>
          <li class="breadcrumb-item active">View</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="card card-outline card-teal shadow-sm">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-info-circle mr-2"></i>Project Information</h3>
        <div class="card-tools">
          <a href="{{ route('projects.index') }}" class="btn btn-default btn-sm mr-1">
            <i class="fas fa-arrow-left mr-1"></i>Back
          </a>
          <a href="{{ route('projects.edit', $project) }}" class="btn btn-teal btn-sm">
            <i class="fas fa-edit mr-1"></i>Edit
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-3 col-sm-6 mb-3">
            <div class="description-block border-right">
              <span class="description-header">{{ optional($project->start_date)->format('d M Y') ?? '—' }}</span>
              <span class="description-text text-uppercase"><i class="fas fa-calendar mr-1 text-teal"></i>Start Date</span>
            </div>
          </div>
          <div class="col-md-3 col-sm-6 mb-3">
            <div class="description-block border-right">
              <span class="description-header">{{ optional($project->end_date)->format('d M Y') ?? '—' }}</span>
              <span class="description-text text-uppercase"><i class="fas fa-calendar-check mr-1 text-teal"></i>End Date</span>
            </div>
          </div>
          <div class="col-md-3 col-sm-6 mb-3">
            <div class="description-block border-right">
              @php
                $statusClass = match($project->status) {
                  'active'    => 'badge-success',
                  'completed' => 'badge-info',
                  default     => 'badge-warning',
                };
              @endphp
              <span class="description-header">
                <span class="badge {{ $statusClass }}">{{ ucfirst($project->status) }}</span>
              </span>
              <span class="description-text text-uppercase"><i class="fas fa-flag mr-1 text-teal"></i>Status</span>
            </div>
          </div>
          <div class="col-md-3 col-sm-6 mb-3">
            <div class="description-block">
              <span class="description-header text-success">₹{{ number_format($project->amount, 2) }}</span>
              <span class="description-text text-uppercase"><i class="fas fa-rupee-sign mr-1 text-teal"></i>Budget</span>
            </div>
          </div>
          @if($project->note)
            <div class="col-12 mt-2">
              <label class="font-weight-bold text-muted text-uppercase" style="font-size:.72rem;letter-spacing:.8px;">
                <i class="fas fa-sticky-note mr-1 text-teal"></i>Note
              </label>
              <p class="mb-0">{{ $project->note }}</p>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</section>
@endsection