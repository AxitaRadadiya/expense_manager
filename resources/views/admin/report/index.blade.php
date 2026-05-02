@extends('admin.layouts.app')
@section('title', 'Reports')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-start" style="gap:1rem;">
      <div class="row mt-3">
        <h1><i class="mr-2 text-teal"></i>Reports</h1>
      </div>
      <a href="{{ route('reports.download', $downloadQuery) }}" class="btn-create mt-3">
        <i class="fas fa-download"></i> Download Excel
      </a>
    </div>
  </div>
</div>

<div class="pull-card">
  <div class="container-fluid" style="padding:0;">
    <div class="main-card mb-4">
      <div class="main-card-title">
        <i class="fas fa-filter"></i> Filters
      </div>

      <div class="main-card-body">
        <form method="GET" action="{{ route('reports.index') }}">
          <div class="row">
            <div class="col-lg-3 col-md-6">
              <div class="form-group">
                <label for="projects_id" class="font-weight-bold">Project</label>
                <select name="projects_id" id="projects_id" class="form-control select2">
                  <option value="">All Projects</option>
                  @foreach($projects as $project)
                  <option value="{{ $project->id }}" {{ (string) ($filters['projects_id'] ?? '') === (string) $project->id ? 'selected' : '' }}>
                    {{ $project->name }}
                  </option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-md-6">
              <div class="form-group">
                <label for="users_id" class="font-weight-bold">User</label>
                <select name="users_id" id="users_id" class="form-control select2">
                  <option value="">All Users</option>
                  @foreach($users as $user)
                  <option value="{{ $user->id }}" {{ (string) ($filters['users_id'] ?? '') === (string) $user->id ? 'selected' : '' }}>
                    {{ $user->name }}
                  </option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-md-6">
              <div class="form-group">
                <label for="from_date" class="font-weight-bold">From Date</label>
                <input type="date" name="from_date" id="from_date" class="form-control" value="{{ $filters['from_date'] ?? '' }}">
              </div>
            </div>

            <div class="col-lg-3 col-md-6">
              <div class="form-group">
                <label for="to_date" class="font-weight-bold">To Date</label>
                <input type="date" name="to_date" id="to_date" class="form-control" value="{{ $filters['to_date'] ?? '' }}">
              </div>
            </div>

            <div class="col-lg-3 col-md-6">
              <div class="form-group">
                <label for="entry_type" class="font-weight-bold">Timeline Type</label>
                <select name="entry_type" id="entry_type" class="form-control select2">
                  <option value="all" {{ ($filters['entry_type'] ?? 'all') === 'all' ? 'selected' : '' }}>All</option>
                  <option value="expense" {{ ($filters['entry_type'] ?? 'all') === 'expense' ? 'selected' : '' }}>Expense</option>
                  <option value="credit" {{ ($filters['entry_type'] ?? 'all') === 'credit' ? 'selected' : '' }}>Credit</option>
                  <option value="transfer" {{ ($filters['entry_type'] ?? 'all') === 'transfer' ? 'selected' : '' }}>Transfer</option>
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
          <div class="main-card-title">
            <i class="fas fa-folder-open"></i> Project Report
          </div>
          <div class="main-card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover report-table mb-0">
                <thead class="thead">
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
          <div class="main-card-title">
            <i class="fas fa-users"></i> User Report
          </div>
          <div class="main-card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover report-table mb-0">
                <thead class="thead">
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
      <div class="main-card-title">
        <i class="fas fa-stream"></i> Timeline Table
      </div>
      <div class="main-card-body">
        <div class="table-responsive">
          <table
            id="ReportTimelineTable"
            class="table table-hover report-table w-100"
            data-url="{{ route('reports.timeline-list') }}"
            data-projects-id="{{ $filters['projects_id'] ?? '' }}"
            data-users-id="{{ $filters['users_id'] ?? '' }}"
            data-from-date="{{ $filters['from_date'] ?? '' }}"
            data-to-date="{{ $filters['to_date'] ?? '' }}"
            data-entry-type="{{ $filters['entry_type'] ?? 'all' }}">
            <thead class="thead">
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

      <div class="main-card mt-4">
        <div class="main-card-title">
          <i class="fas fa-user-clock"></i> Labour Management
        </div>
        <div class="main-card-body">
          <div class="table-responsive">
            <table id="LabourTable" class="table table-hover report-table mb-0">
              <thead class="thead">
                <tr>
                  <th>Sr No.</th>
                  <th>Project</th>
                  <th>Vendor</th>
                  <th>Total Labour</th>
                  <th>Start Date</th>
                  <th>End Date</th>
                  <th>Amount</th>
                </tr>
              </thead>
              <tbody>
                @forelse($labourEntries as $entry)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ optional($entry->project)->name ?? '-' }}</td>
                  <td>{{ optional($entry->vendor)->name ?? '-' }}</td>
                  <td>{{ $entry->total_labour ?? '-' }}</td>
                  <td>{{ $entry->start_date ? $entry->start_date->format('d-m-Y') : '-' }}</td>
                  <td>{{ $entry->end_date ? $entry->end_date->format('d-m-Y') : '-' }}</td>
                  <td class="text-danger font-weight-bold">Rs. {{ number_format((float) $entry->amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                  <td colspan="7" class="empty-table">No labour records found.</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
  </div>
</div>
@endsection