@extends('admin.layouts.app')
@section('title', 'Activity Logs')

@section('content')

<style>
  .page-hero {
    background: linear-gradient(135deg,#0B1120 0%,#111C30 60%,#162244 100%);
    padding:1.6rem 2rem 4.2rem; position:relative; overflow:hidden;
  }
  .page-hero::before { content:''; position:absolute; inset:0; pointer-events:none; background-image:radial-gradient(rgba(201,150,12,.15) 1px,transparent 1px); background-size:26px 26px; }
  .page-hero .orb { position:absolute; border-radius:50%; pointer-events:none; width:200px; height:200px; background:radial-gradient(circle,rgba(201,150,12,.2) 0%,transparent 65%); top:-60px; right:40px; }
  .page-hero h1 { font-family:'Playfair Display',serif; font-size:1.5rem; font-weight:800; color:#fff; margin:0 0 .25rem; position:relative; z-index:2; }
  .page-hero p  { color:rgba(255,255,255,.4); font-size:.82rem; margin:0; position:relative; z-index:2; }

  .pull-card { margin-top:-2.4rem; position:relative; z-index:10; padding:0 1.5rem; }

  /* Filter bar */
  .filter-bar {
    background:#fff; border-radius:14px; box-shadow:0 4px 20px rgba(0,0,0,.07);
    border:1px solid rgba(0,0,0,.04); padding:1rem 1.4rem;
    display:flex; align-items:center; flex-wrap:wrap; gap:.75rem;
    margin-bottom:1.25rem;
  }
  .filter-bar .finput, .filter-bar .fselect {
    border:1.5px solid #dde2ec; border-radius:8px;
    padding:.42rem .85rem; font-size:.83rem; color:#0D1A30;
    background:#fff; outline:none;
    transition:border-color .2s, box-shadow .2s;
  }
  .filter-bar .finput:focus, .filter-bar .fselect:focus {
    border-color:#C9960C; box-shadow:0 0 0 3px rgba(201,150,12,.12);
  }
  .filter-bar .finput { min-width:200px; }
  .btn-filter {
    background:linear-gradient(135deg,#9A6E00,#F5BE2E); color:#111;
    border:none; border-radius:8px; padding:.45rem 1.1rem;
    font-size:.82rem; font-weight:700; cursor:pointer;
    display:inline-flex; align-items:center; gap:.4rem;
    box-shadow:0 3px 10px rgba(201,150,12,.25);
    transition:transform .18s, box-shadow .18s;
  }
  .btn-filter:hover { transform:translateY(-1px); box-shadow:0 5px 16px rgba(201,150,12,.35); }
  .btn-clear { background:#f1f5f9; color:#64748b; border:1.5px solid #e2e8f0; border-radius:8px; padding:.43rem 1rem; font-size:.82rem; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:.35rem; }
  .btn-clear:hover { background:#e2e8f0; }

  /* Main card */
  .main-card { background:#fff; border-radius:16px; box-shadow:0 6px 32px rgba(0,0,0,.1); border:1px solid rgba(0,0,0,.04); overflow:hidden; }
  .main-card-head { padding:1.1rem 1.4rem; border-bottom:1px solid #f0f2f7; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.75rem; }
  .main-card-title { font-size:.95rem; font-weight:800; color:#0D1A30; display:flex; align-items:center; gap:.5rem; }
  .main-card-title i { color:#C9960C; }
  .main-card-body { padding:0; }

  /* Table */
  .log-table { width:100%; border-collapse:collapse; }
  .log-table thead th {
    background:#0D1A30; color:#fff;
    font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.7px;
    padding:.72rem 1rem; white-space:nowrap;
    border-bottom:2px solid #C9960C;
  }
  .log-table tbody tr { border-bottom:1px solid #f0f2f7; transition:background .15s; }
  .log-table tbody tr:hover { background:#fdfaf0; }
  .log-table tbody td { padding:.7rem 1rem; font-size:.82rem; color:#2d3748; vertical-align:middle; }

  /* Action badges */
  .ab { display:inline-flex; align-items:center; gap:.3rem; border-radius:20px; padding:.22rem .75rem; font-size:.71rem; font-weight:700; text-transform:capitalize; white-space:nowrap; }
  .ab-green  { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
  .ab-blue   { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }
  .ab-gold   { background:#fdf8e8; color:#9A6E00; border:1px solid rgba(201,150,12,.3); }
  .ab-orange { background:#fff7ed; color:#c2410c; border:1px solid #fed7aa; }
  .ab-red    { background:#fff1f2; color:#be123c; border:1px solid #fecdd3; }
  .ab-grey   { background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; }

  /* Model chip */
  .model-chip { background:#f0f4ff; color:#4f46e5; border:1px solid #c7d2fe; border-radius:6px; padding:.15rem .55rem; font-size:.72rem; font-weight:700; }

  /* Pagination */
  .pagination { display:flex; gap:.35rem; flex-wrap:wrap; }
  .page-link { border:1.5px solid #e2e8f0; border-radius:7px; padding:.35rem .75rem; font-size:.8rem; color:#64748b; text-decoration:none; transition:all .15s; display:inline-flex; align-items:center; }
  .page-link:hover,.page-item.active .page-link { background:#C9960C; border-color:#C9960C; color:#fff; }

  /* View button */
  .btn-view { color:#C9960C; font-size:.82rem; text-decoration:none; display:inline-flex; align-items:center; gap:.3rem; font-weight:600; }
  .btn-view:hover { color:#9A6E00; }
</style>

<div class="page-hero">
  <div class="orb"></div>
  <div class="container-fluid" style="position:relative;z-index:2;">
    <h1><i class="fas fa-history mr-2" style="color:#C9960C;font-size:1.1rem;"></i>Activity Logs</h1>
    <p>Full audit trail of all system actions.</p>
  </div>
</div>

<div class="pull-card">
  <div class="container-fluid" style="padding:0;">

    {{-- ── Filter Bar ── --}}
    <form method="GET" action="{{ route('activity-logs.index') }}" class="filter-bar">
      <input type="text" name="search" class="finput" placeholder="Search user, description…" value="{{ request('search') }}">

      <select name="action" class="fselect">
        <option value="">All Actions</option>
        @foreach($actions as $a)
          <option value="{{ $a }}" {{ request('action') === $a ? 'selected' : '' }}>{{ ucfirst($a) }}</option>
        @endforeach
      </select>

      <input type="date" name="date_from" class="finput" style="min-width:auto;" value="{{ request('date_from') }}" title="From date">
      <input type="date" name="date_to"   class="finput" style="min-width:auto;" value="{{ request('date_to') }}"   title="To date">

      <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Filter</button>
      <a href="{{ route('activity-logs.index') }}" class="btn-clear"><i class="fas fa-times"></i> Clear</a>
    </form>

    {{-- ── Table ── --}}
    <div class="main-card">
      <div class="main-card-head">
        <div class="main-card-title">
          <i class="fas fa-list-alt"></i> All Activity
          <span style="background:#fdf8e8;color:#9A6E00;border:1px solid rgba(201,150,12,.25);border-radius:6px;padding:.1rem .6rem;font-size:.72rem;font-weight:800;">
            {{ $logs->total() }}
          </span>
        </div>
      </div>

      <div class="main-card-body">
        <div class="table-responsive">
          <table class="log-table">
            <thead>
              <tr>
                <th>#</th>
                <th>User</th>
                <th>Action</th>
                <th>Description</th>
                <th>Model</th>
                <th>Date & Time</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @forelse($logs as $i => $log)
              <tr>
                <td style="color:#94a3b8;font-size:.78rem;">{{ $logs->firstItem() + $i }}</td>

                {{-- User --}}
                <td>
                  <div style="display:flex;align-items:center;gap:.5rem;">
                    <div style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,#9A6E00,#C9960C);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.6rem;font-weight:800;flex-shrink:0;">
                      {{ strtoupper(substr($log->user_name ?? '?', 0, 1)) }}
                    </div>
                    <span style="font-weight:600;color:#0D1A30;font-size:.82rem;">{{ $log->user_name ?? '—' }}</span>
                  </div>
                </td>

                {{-- Action badge --}}
                <td>
                  <span class="ab ab-{{ $log->action_color }}">
                    @php
                      $icons = ['login'=>'sign-in-alt','logout'=>'sign-out-alt','created'=>'plus','updated'=>'pen','deleted'=>'trash'];
                    @endphp
                    <i class="fas fa-{{ $icons[$log->action] ?? 'circle' }}"></i>
                    {{ $log->action_label }}
                  </span>
                </td>

                {{-- Description --}}
                <td style="max-width:280px;">
                  <span style="color:#475569;font-size:.81rem;">{{ Str::limit($log->description, 70) }}</span>
                </td>

                {{-- Model --}}
                <td>
                  @if($log->model_type)
                    <span class="model-chip">{{ $log->model_name }}</span>
                    @if($log->model_label)
                      <div style="color:#94a3b8;font-size:.74rem;margin-top:.2rem;">{{ Str::limit($log->model_label, 30) }}</div>
                    @endif
                  @else
                    <span style="color:#cbd5e1;">—</span>
                  @endif
                </td>

                {{-- Date --}}
                <td style="white-space:nowrap;">
                  <div style="font-size:.82rem;color:#0D1A30;font-weight:600;">{{ $log->created_at->format('d M Y') }}</div>
                  <div style="font-size:.74rem;color:#94a3b8;">{{ $log->created_at->format('h:i A') }}</div>
                </td>

                {{-- View --}}
                <td>
                  <a href="{{ route('activity-logs.show', $log->id) }}" class="btn-view">
                    <i class="fas fa-eye"></i> View
                  </a>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" style="text-align:center;padding:3rem;color:#94a3b8;">
                  <i class="fas fa-history" style="font-size:2rem;display:block;margin-bottom:.75rem;"></i>
                  No activity logs found.
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        {{-- Pagination --}}
        @if($logs->hasPages())
        <div style="padding:1rem 1.4rem;border-top:1px solid #f0f2f7;">
          {{ $logs->links('pagination::bootstrap-4') }}
        </div>
        @endif
      </div>
    </div>

  </div>
</div>
<div style="height:2rem;"></div>
@endsection