@extends('admin.layouts.app')
@section('title', 'Activity Logs')

@section('content')

<style>
  .page-hero {
    background: linear-gradient(135deg, #006666 0%, #008d8d 55%, #00a8a8 100%);
    padding: 1.6rem 2rem 4.2rem; position: relative; overflow: hidden;
  }
  .page-hero::before { content:''; position:absolute; inset:0; pointer-events:none; background-image:radial-gradient(rgba(255,255,255,.07) 1px,transparent 1px); background-size:26px 26px; }
  .page-hero .orb { position:absolute; border-radius:50%; pointer-events:none; width:200px; height:200px; background:radial-gradient(circle,rgba(255,255,255,.12) 0%,transparent 65%); top:-60px; right:40px; }
  .page-hero h1 { font-family:'Playfair Display',serif; font-size:1.5rem; font-weight:800; color:#fff; margin:0 0 .25rem; position:relative; z-index:2; }
  .page-hero p  { color:rgba(255,255,255,.6); font-size:.82rem; margin:0; position:relative; z-index:2; }

  .pull-card { margin-top:-2.4rem; position:relative; z-index:10; padding:0 1.5rem; }

  /* ── Filter Bar ── */
  .filter-bar {
    background:#fff; border-radius:14px;
    box-shadow:0 4px 20px rgba(0,141,141,.10); border:1px solid #d0eded;
    padding:1.1rem 1.4rem; margin-bottom:1.25rem;
    display:flex; align-items:flex-end; flex-wrap:wrap; gap:.85rem;
  }
  .filter-group { display:flex; flex-direction:column; gap:.25rem; }
  .filter-label { font-size:.68rem; font-weight:700; color:#5a8080; text-transform:uppercase; letter-spacing:.8px; }
  .finput, .fselect {
    border:1.5px solid #c8e6e6; border-radius:8px;
    padding:.42rem .85rem; font-size:.83rem; color:#0d2e2e;
    background:#fff; outline:none; height:36px;
    transition:border-color .2s, box-shadow .2s;
  }
  .finput:focus, .fselect:focus { border-color:#008d8d; box-shadow:0 0 0 3px rgba(0,141,141,.12); }
  .finput.search-f { min-width:210px; }
  .finput.date-f   { min-width:148px; }
  .fselect.action-f { min-width:145px; cursor:pointer; }

  .btn-filter {
    background:linear-gradient(135deg,#006666,#00b5b5); color:#fff;
    border:none; border-radius:8px; padding:0 1.2rem;
    font-size:.82rem; font-weight:700; cursor:pointer; height:36px;
    display:inline-flex; align-items:center; gap:.4rem;
    box-shadow:0 3px 10px rgba(0,141,141,.25);
    transition:transform .18s, box-shadow .18s;
  }
  .btn-filter:hover { transform:translateY(-1px); box-shadow:0 5px 16px rgba(0,141,141,.35); }
  .btn-clear {
    background:#f0fafa; color:#2a5050; border:1.5px solid #c8e6e6;
    border-radius:8px; padding:0 1rem; height:36px; font-size:.82rem; font-weight:600;
    text-decoration:none; display:inline-flex; align-items:center; gap:.35rem;
    transition:background .15s;
  }
  .btn-clear:hover { background:#e0f7f7; color:#006666; }
  .filter-sep { width:1px; height:36px; background:#c8e6e6; flex-shrink:0; align-self:flex-end; }

  /* Active filter pills */
  .filter-pills { display:flex; flex-wrap:wrap; gap:.5rem; margin-bottom:1rem; }
  .fpill {
    background:#e0f7f7; color:#006666; border:1px solid #a0d8d8;
    border-radius:20px; padding:.2rem .85rem; font-size:.74rem; font-weight:600;
    display:inline-flex; align-items:center; gap:.3rem;
  }

  /* ── Main card ── */
  .main-card { background:#fff; border-radius:16px; box-shadow:0 6px 32px rgba(0,141,141,.10); border:1px solid #d0eded; overflow:hidden; }
  .main-card-head { padding:1.1rem 1.4rem; border-bottom:1px solid #e4f0f0; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.75rem; background:#f9fdfd; }
  .main-card-title { font-size:.95rem; font-weight:800; color:#0d2e2e; display:flex; align-items:center; gap:.5rem; }
  .main-card-title i { color:#008d8d; }
  .main-card-body { padding:0; }
  .count-badge { background:#e0f7f7; color:#006666; border:1px solid #a0d8d8; border-radius:6px; padding:.1rem .6rem; font-size:.72rem; font-weight:800; }

  /* ── Log Table ── */
  .log-table { width:100%; border-collapse:collapse; }
  .log-table thead th { background:#008d8d; color:#fff; font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.7px; padding:.72rem 1rem; white-space:nowrap; border-bottom:2px solid #006666; }
  .log-table tbody tr { border-bottom:1px solid #e4f0f0; transition:background .15s; }
  .log-table tbody tr:hover { background:#e0f7f7; }
  .log-table tbody td { padding:.7rem 1rem; font-size:.82rem; color:#0d2e2e; vertical-align:middle; }

  /* Action badges */
  .ab { display:inline-flex; align-items:center; gap:.3rem; border-radius:20px; padding:.22rem .75rem; font-size:.71rem; font-weight:700; text-transform:capitalize; white-space:nowrap; }
  .ab-green  { background:#e8f8f0; color:#15803d; border:1px solid #bbf7d0; }
  .ab-blue   { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }
  .ab-gold   { background:#e0f7f7; color:#006666; border:1px solid #a0d8d8; }
  .ab-orange { background:#fff7ed; color:#c2410c; border:1px solid #fed7aa; }
  .ab-red    { background:#fff1f2; color:#be123c; border:1px solid #fecdd3; }
  .ab-grey   { background:#f0fafa; color:#5a8080; border:1px solid #c8e6e6; }

  .model-chip { background:#e0f7f7; color:#006666; border:1px solid #a0d8d8; border-radius:6px; padding:.15rem .55rem; font-size:.72rem; font-weight:700; }
  .u-avatar { width:28px; height:28px; border-radius:50%; background:linear-gradient(135deg,#006666,#008d8d); display:flex; align-items:center; justify-content:center; color:#fff; font-size:.6rem; font-weight:800; flex-shrink:0; }
  .btn-view { color:#008d8d; font-size:.8rem; text-decoration:none; display:inline-flex; align-items:center; gap:.3rem; font-weight:600; background:#e0f7f7; border:1px solid #a0d8d8; border-radius:6px; padding:.22rem .7rem; transition:background .15s; }
  .btn-view:hover { background:#c0eded; color:#006666; }

  /* Pagination */
  .pagination .page-link { border:1.5px solid #c8e6e6; border-radius:7px; padding:.35rem .75rem; font-size:.8rem; color:#2a5050; transition:all .15s; }
  .pagination .page-item.active .page-link,
  .pagination .page-link:hover { background:#008d8d !important; border-color:#008d8d !important; color:#fff !important; }

  .empty-state { text-align:center; padding:3rem; color:#5a8080; }
  .empty-state i { font-size:2rem; display:block; margin-bottom:.75rem; opacity:.3; }
</style>

{{-- HERO --}}
<div class="page-hero">
  <div class="orb"></div>
  <div class="container-fluid" style="position:relative;z-index:2;">
    <h1><i class="fas fa-history mr-2" style="color:rgba(255,255,255,.85);font-size:1.1rem;"></i>Activity Logs</h1>
    <p>Full audit trail of all system actions.</p>
  </div>
</div>

<div class="pull-card">
  <div class="container-fluid" style="padding:0;">

    {{-- ── Filter Bar ── --}}
    <form method="GET" action="{{ route('activity-logs.index') }}" class="filter-bar">

      <div class="filter-group">
        <span class="filter-label"><i class="fas fa-search" style="font-size:.6rem;"></i> Search</span>
        <input type="text" name="search" class="finput search-f"
               placeholder="User name or description…"
               value="{{ request('search') }}">
      </div>

      <div class="filter-sep d-none d-sm-block"></div>

      <div class="filter-group">
        <span class="filter-label"><i class="fas fa-bolt" style="font-size:.6rem;"></i> Action</span>
        <select name="action" class="fselect action-f">
          <option value="">All Actions</option>
          @foreach($actions as $a)
            <option value="{{ $a }}" {{ request('action') === $a ? 'selected' : '' }}>
              {{ ucfirst($a) }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="filter-sep d-none d-sm-block"></div>

      <div class="filter-group">
        <span class="filter-label"><i class="fas fa-calendar" style="font-size:.6rem;"></i> From Date</span>
        <input type="date" name="date_from" class="finput date-f" value="{{ request('date_from') }}">
      </div>

      <div class="filter-group">
        <span class="filter-label"><i class="fas fa-calendar" style="font-size:.6rem;"></i> To Date</span>
        <input type="date" name="date_to" class="finput date-f" value="{{ request('date_to') }}">
      </div>

      <div class="filter-sep d-none d-sm-block"></div>

      <div class="filter-group" style="flex-direction:row;gap:.5rem;">
        <button type="submit" class="btn-filter">
          <i class="fas fa-search"></i> Filter
        </button>
        <a href="{{ route('activity-logs.index') }}" class="btn-clear">
          <i class="fas fa-times"></i> Clear
        </a>
      </div>

    </form>

    {{-- Active filter pills --}}
    @if(request('search') || request('action') || request('date_from') || request('date_to'))
    <div class="filter-pills">
      @if(request('search'))
        <span class="fpill"><i class="fas fa-search" style="font-size:.62rem;"></i> "{{ request('search') }}"</span>
      @endif
      @if(request('action'))
        <span class="fpill"><i class="fas fa-bolt" style="font-size:.62rem;"></i> {{ ucfirst(request('action')) }}</span>
      @endif
      @if(request('date_from'))
        <span class="fpill"><i class="fas fa-calendar-alt" style="font-size:.62rem;"></i> From: {{ \Carbon\Carbon::parse(request('date_from'))->format('d M Y') }}</span>
      @endif
      @if(request('date_to'))
        <span class="fpill"><i class="fas fa-calendar-alt" style="font-size:.62rem;"></i> To: {{ \Carbon\Carbon::parse(request('date_to'))->format('d M Y') }}</span>
      @endif
      <a href="{{ route('activity-logs.index') }}" style="font-size:.74rem;color:#be123c;text-decoration:none;display:inline-flex;align-items:center;gap:.25rem;font-weight:600;">
        <i class="fas fa-times-circle"></i> Clear all
      </a>
    </div>
    @endif

    {{-- ── Table Card ── --}}
    <div class="main-card">
      <div class="main-card-head">
        <div class="main-card-title">
          <i class="fas fa-list-alt"></i> All Activity
          <span class="count-badge">{{ $logs->total() }}</span>
        </div>
        <div style="font-size:.78rem;color:#5a8080;">
          @if($logs->total())
            Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }}
          @endif
        </div>
      </div>

      <div class="main-card-body">
        <div class="table-responsive">
          <table class="log-table">
            <thead>
              <tr>
                <th width="40">#</th>
                <th>User</th>
                <th>Action</th>
                <th>Description</th>
                <th>Model</th>
                <th>Date & Time</th>
                <th width="70"></th>
              </tr>
            </thead>
            <tbody>
              @forelse($logs as $i => $log)
              @php $icons = ['login'=>'sign-in-alt','logout'=>'sign-out-alt','created'=>'plus','updated'=>'pen','deleted'=>'trash']; @endphp
              <tr>
                <td style="color:#5a8080;font-size:.76rem;">{{ $logs->firstItem() + $i }}</td>

                <td>
                  <div style="display:flex;align-items:center;gap:.5rem;">
                    <div class="u-avatar">{{ strtoupper(substr($log->user_name ?? '?', 0, 1)) }}</div>
                    <span style="font-weight:600;font-size:.82rem;">{{ $log->user_name ?? '—' }}</span>
                  </div>
                </td>

                <td>
                  <span class="ab ab-{{ $log->action_color }}">
                    <i class="fas fa-{{ $icons[$log->action] ?? 'circle' }}"></i>
                    {{ $log->action_label }}
                  </span>
                </td>

                <td style="max-width:260px;">
                  <span style="color:#2a5050;font-size:.81rem;">{{ Str::limit($log->description, 70) }}</span>
                </td>

                <td>
                  @if($log->model_type)
                    <span class="model-chip">{{ $log->model_name }}</span>
                    @if($log->model_label)
                      <div style="color:#5a8080;font-size:.72rem;margin-top:.2rem;">{{ Str::limit($log->model_label, 28) }}</div>
                    @endif
                  @else
                    <span style="color:#a0cece;">—</span>
                  @endif
                </td>

                <td style="white-space:nowrap;">
                  <div style="font-size:.82rem;font-weight:600;">{{ $log->created_at->format('d M Y') }}</div>
                  <div style="font-size:.73rem;color:#5a8080;">{{ $log->created_at->format('h:i A') }}</div>
                </td>

                <td>
                  <a href="{{ route('activity-logs.show', $log->id) }}" class="btn-view">
                    <i class="fas fa-eye"></i> View
                  </a>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7">
                  <div class="empty-state">
                    <i class="fas fa-history"></i>
                    No activity logs found.
                    @if(request()->hasAny(['search','action','date_from','date_to']))
                      <div style="margin-top:.4rem;font-size:.78rem;">Try clearing the filters above.</div>
                    @endif
                  </div>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if($logs->hasPages())
        <div style="padding:1rem 1.4rem;border-top:1px solid #e4f0f0;">
          {{ $logs->links('pagination::bootstrap-4') }}
        </div>
        @endif
      </div>
    </div>

  </div>
</div>
<div style="height:2rem;"></div>
@endsection