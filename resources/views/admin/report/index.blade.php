@extends('admin.layouts.app')
@section('title', 'Reports')

@php
  $selectedProject = old('projects_id', $filters['projects_id'] ?? '');
  $selectedUser = old('users_id', $filters['users_id'] ?? '');
  $fromDate = old('from_date', $filters['from_date'] ?? '');
  $toDate = old('to_date', $filters['to_date'] ?? '');
  $selectedType = old('entry_type', $filters['entry_type'] ?? 'all');
  $downloadQuery = array_filter([
    'projects_id' => $selectedProject,
    'users_id' => $selectedUser,
    'from_date' => $fromDate,
    'to_date' => $toDate,
    'entry_type' => $selectedType,
  ], fn ($value) => filled($value) && $value !== 'all');
@endphp

@section('content')
<div class="page-hero">
  <div class="orb"></div>
  <div class="container-fluid" style="position:relative;z-index:2;">
    <div class="d-flex flex-wrap justify-content-between align-items-start" style="gap:1rem;">
      <div>
        <h1><i class="fas fa-chart-line mr-2" style="color:rgba(255,255,255,.85);font-size:1.1rem;"></i>Reports</h1>
        <p>Project and user summary with separate expense, credit, and timeline tables.</p>
      </div>
      <a href="{{ route('reports.download', $downloadQuery) }}" class="btn-create">
        <i class="fas fa-download"></i> Download Excel
      </a>
    </div>
  </div>
</div>

<div class="pull-card">
  <div class="container-fluid" style="padding:0;">
    <div class="main-card mb-4">
      <div class="main-card-head">
        <div class="main-card-title">
          <i class="fas fa-filter"></i> Filters
        </div>
      </div>

      <div class="main-card-body">
        <form method="GET" action="{{ route('reports.index') }}">
          <div class="row">
            <div class="col-lg-3 col-md-6">
              <div class="form-group">
                <label for="projects_id" class="font-weight-bold">Project</label>
                <select name="projects_id" id="projects_id" class="form-control">
                  <option value="">All Projects</option>
                  @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ (string) $selectedProject === (string) $project->id ? 'selected' : '' }}>
                      {{ $project->name }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-md-6">
              <div class="form-group">
                <label for="users_id" class="font-weight-bold">User</label>
                <select name="users_id" id="users_id" class="form-control">
                  <option value="">All Users</option>
                  @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ (string) $selectedUser === (string) $user->id ? 'selected' : '' }}>
                      {{ $user->name }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-md-6">
              <div class="form-group">
                <label for="from_date" class="font-weight-bold">From Date</label>
                <input type="date" name="from_date" id="from_date" class="form-control" value="{{ $fromDate }}">
              </div>
            </div>

            <div class="col-lg-3 col-md-6">
              <div class="form-group">
                <label for="to_date" class="font-weight-bold">To Date</label>
                <input type="date" name="to_date" id="to_date" class="form-control" value="{{ $toDate }}">
              </div>
            </div>

            <div class="col-lg-3 col-md-6">
              <div class="form-group">
                <label for="entry_type" class="font-weight-bold">Timeline Type</label>
                <select name="entry_type" id="entry_type" class="form-control">
                  <option value="all" {{ $selectedType === 'all' ? 'selected' : '' }}>All</option>
                  <option value="expense" {{ $selectedType === 'expense' ? 'selected' : '' }}>Expense</option>
                  <option value="credit" {{ $selectedType === 'credit' ? 'selected' : '' }}>Credit</option>
                  <option value="transfer" {{ $selectedType === 'transfer' ? 'selected' : '' }}>Transfer</option>
                </select>
              </div>
            </div>
          </div>

          <div class="report-actions">
            <button type="submit" class="btn-submit">
              <i class="fas fa-search"></i> Apply Filter
            </button>
            <a href="{{ route('reports.index') }}" class="btn-cancel">
              <i class="fas fa-undo"></i> Reset
            </a>
          </div>
        </form>
      </div>
    </div>

    <div class="row">
      <div class="col-xl-6 mb-4">
        <div class="main-card h-100">
          <div class="main-card-head">
            <div class="main-card-title">
              <i class="fas fa-folder-open"></i> Project Report
            </div>
          </div>
          <div class="main-card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover report-table mb-0">
                <thead>
                  <tr>
                    <th>Project</th>
                    <th>Expense</th>
                    <th>Credit</th>
                    <th>Current Balance</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($projectSummary as $item)
                    <tr>
                      <td>{{ $item->project_name }}</td>
                      <td class="text-danger font-weight-bold">Rs. {{ number_format((float) $item->total_expense, 2) }}</td>
                      <td class="text-success font-weight-bold">Rs. {{ number_format((float) $item->total_credit, 2) }}</td>
                      <td class="font-weight-bold {{ $item->current_balance >= 0 ? 'text-info' : 'text-warning' }}">
                        Rs. {{ number_format((float) $item->current_balance, 2) }}
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="4" class="empty-table">No project data found.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-6 mb-4">
        <div class="main-card h-100">
          <div class="main-card-head">
            <div class="main-card-title">
              <i class="fas fa-users"></i> User Report
            </div>
          </div>
          <div class="main-card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover report-table mb-0">
                <thead>
                  <tr>
                    <th>User</th>
                    <th>Expense</th>
                    <th>Credit</th>
                    <th>Current Balance</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($userSummary as $item)
                    <tr>
                      <td>{{ $item->user_name }}</td>
                      <td class="text-danger font-weight-bold">Rs. {{ number_format((float) $item->total_expense, 2) }}</td>
                      <td class="text-success font-weight-bold">Rs. {{ number_format((float) $item->total_credit, 2) }}</td>
                      <td class="font-weight-bold {{ $item->current_balance >= 0 ? 'text-info' : 'text-warning' }}">
                        Rs. {{ number_format((float) $item->current_balance, 2) }}
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="4" class="empty-table">No user data found.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="main-card">
      <div class="main-card-head">
        <div class="main-card-title">
          <i class="fas fa-stream"></i> Timeline Table
        </div>
      </div>
      <div class="main-card-body">
        <div class="table-responsive">
          <table
            id="ReportTimelineTable"
            class="table table-hover report-table w-100"
            data-url="{{ route('reports.timeline-list') }}"
            data-projects-id="{{ $selectedProject }}"
            data-users-id="{{ $selectedUser }}"
            data-from-date="{{ $fromDate }}"
            data-to-date="{{ $toDate }}"
            data-entry-type="{{ $selectedType }}"
          >
            <thead>
              <tr>
                <th>Sr No.</th>
                <th>Type</th>
                <th>Date</th>
                <th>Time</th>
                <th>Project</th>
                <th>User</th>
                <th>Amount</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
