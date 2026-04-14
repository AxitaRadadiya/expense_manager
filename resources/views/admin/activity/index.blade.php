@extends('admin.layouts.app')
@section('title', 'Activity Logs')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mt-3">
        <h1><i class="mr-2 text-teal"></i>Activity Logs</h1>
    </div>
  </div>
</div>

<div class="pull-card">
  <div class="container-fluid" style="padding:0;">
    <div class="filter-card mb-3">
      <div class="filter-card-head">
        <div class="filter-card-title">
          <i class="fas fa-filter"></i> Filters
        </div>
      </div>
      <div class="main-card-body">
        <form method="GET" action="{{ route('activity-logs.index') }}">
          <div class="row align-items-end">
            <div class="col-12 col-md-4 col-lg-3 mb-3">
              <label>Search</label>
              <input type="text" name="search" class="form-control form-control-sm" placeholder="User name or description..." value="{{ request('search') }}">
            </div>
            <div class="col-12 col-md-3 col-lg-2 mb-3">
              <label>Action</label>
              <select name="action" class="form-control form-control-sm">
                <option value="">All Actions</option>
                @foreach($actions as $a)
                  <option value="{{ $a }}" {{ request('action') === $a ? 'selected' : '' }}>{{ ucfirst($a) }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12 col-md-3 col-lg-2 mb-3">
              <label>From Date</label>
              <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            </div>
            <div class="col-12 col-md-3 col-lg-2 mb-3">
              <label>To Date</label>
              <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>
            <div class="col-12 col-lg-3 mb-3 page-actions">
              <button type="submit" class="btn-submit mr-2"><i class="fas fa-search mr-1"></i>Filter</button>
              <a href="{{ route('activity-logs.index') }}" class="btn-cancel"><i class="fas fa-times mr-1"></i>Clear</a>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="main-card table-card">
        @if($logs->total())
          <div class="page-note">Showing {{ $logs->firstItem() }}-{{ $logs->lastItem() }} of {{ $logs->total() }}</div>
        @endif

      <div class="main-card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="thead">
              <tr>
                <th>#</th>
                <th>User</th>
                <th>Action</th>
                <th>Description</th>
                <th>Model</th>
                <th>Date &amp; Time</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @forelse($logs as $i => $log)
                @php
                  $actionBadge = match($log->action) {
                    'login' => 'badge-success',
                    'logout' => 'badge-secondary',
                    'created' => 'badge-primary',
                    'updated' => 'badge-info',
                    'deleted' => 'badge-danger',
                    default => 'badge-light',
                  };
                  $icons = ['login' => 'sign-in-alt', 'logout' => 'sign-out-alt', 'created' => 'plus', 'updated' => 'pen', 'deleted' => 'trash'];
                @endphp
                <tr>
                  <td class="text-muted">{{ $logs->firstItem() + $i }}</td>
                  <td><span class="font-weight-bold">{{ $log->user_name ?? '-' }}</span></td>
                  <td>
                    <span class="badge {{ $actionBadge }}">
                      <i class="fas fa-{{ $icons[$log->action] ?? 'circle' }} mr-1"></i>{{ $log->action_label }}
                    </span>
                  </td>
                  <td class="text-muted">{{ Str::limit($log->description, 70) }}</td>
                  <td>
                    @if($log->model_type)
                      <span class="badge badge-secondary">{{ $log->model_name }}</span>
                      @if($log->model_label)
                        <div class="page-note mt-1">{{ Str::limit($log->model_label, 28) }}</div>
                      @endif
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td class="text-nowrap">
                    <div class="font-weight-bold">{{ $log->created_at->format('d-m-Y') }}</div>
                    <div class="page-note">{{ $log->created_at->format('h:i A') }}</div>
                  </td>
                  <td>
                    <div class="table-action-group">
                      <a href="{{ route('activity-logs.show', $log->id) }}" class="table-action-btn is-view" title="View">
                        <i class="fas fa-eye"></i>
                      </a>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="empty-state">
                    <i class="fas fa-history"></i>
                    No activity logs found.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if($logs->hasPages())
          <div class="p-3">
            {{ $logs->links('pagination::bootstrap-4') }}
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
