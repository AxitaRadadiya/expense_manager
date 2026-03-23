@extends('admin.layouts.app')
@section('title', 'Activity Detail')

@section('content')

<style>
  .page-hero { background:linear-gradient(135deg,#0B1120 0%,#111C30 60%,#162244 100%); padding:1.6rem 2rem 4.2rem; position:relative; overflow:hidden; }
  .page-hero::before { content:''; position:absolute; inset:0; pointer-events:none; background-image:radial-gradient(rgba(201,150,12,.15) 1px,transparent 1px); background-size:26px 26px; }
  .page-hero .orb { position:absolute; border-radius:50%; pointer-events:none; width:180px; height:180px; background:radial-gradient(circle,rgba(201,150,12,.2) 0%,transparent 65%); top:-50px; right:40px; }
  .page-hero h1 { font-family:'Playfair Display',serif; font-size:1.45rem; font-weight:800; color:#fff; margin:0 0 .25rem; position:relative; z-index:2; }
  .page-hero p { color:rgba(255,255,255,.4); font-size:.82rem; margin:0; position:relative; z-index:2; }

  .pull-card { margin-top:-2.4rem; position:relative; z-index:10; padding:0 1.5rem; }

  .detail-card { background:#fff; border-radius:16px; box-shadow:0 6px 32px rgba(0,0,0,.1); border:1px solid rgba(0,0,0,.04); overflow:hidden; margin-bottom:1.25rem; }
  .detail-head { padding:1rem 1.4rem; border-bottom:1px solid #f0f2f7; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.6rem; }
  .detail-title { font-size:.92rem; font-weight:800; color:#0D1A30; display:flex; align-items:center; gap:.5rem; }
  .detail-title i { color:#C9960C; }
  .detail-body { padding:1.4rem; }

  /* Meta grid */
  .meta-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; }
  @media(max-width:768px){ .meta-grid { grid-template-columns:1fr 1fr; } }
  @media(max-width:480px){ .meta-grid { grid-template-columns:1fr; } }

  .meta-item { background:#f8fafc; border-radius:10px; padding:.9rem 1rem; }
  .meta-label { font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.8px; color:#8a98b4; margin-bottom:.35rem; }
  .meta-value { font-size:.88rem; font-weight:600; color:#0D1A30; word-break:break-all; }

  /* Action badge */
  .ab { display:inline-flex; align-items:center; gap:.3rem; border-radius:20px; padding:.25rem .85rem; font-size:.75rem; font-weight:700; }
  .ab-green  { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
  .ab-blue   { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }
  .ab-gold   { background:#fdf8e8; color:#9A6E00; border:1px solid rgba(201,150,12,.3); }
  .ab-orange { background:#fff7ed; color:#c2410c; border:1px solid #fed7aa; }
  .ab-red    { background:#fff1f2; color:#be123c; border:1px solid #fecdd3; }
  .ab-grey   { background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; }

  /* Diff table */
  .diff-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
  @media(max-width:640px){ .diff-grid { grid-template-columns:1fr; } }

  .diff-panel { border-radius:10px; overflow:hidden; border:1px solid #e8eaf0; }
  .diff-panel-head { padding:.6rem 1rem; font-size:.72rem; font-weight:800; text-transform:uppercase; letter-spacing:.8px; }
  .diff-panel-head.old { background:#fff1f2; color:#be123c; }
  .diff-panel-head.new { background:#f0fdf4; color:#15803d; }
  .diff-table { width:100%; border-collapse:collapse; }
  .diff-table tr { border-bottom:1px solid #f0f2f7; }
  .diff-table tr:last-child { border-bottom:none; }
  .diff-table td { padding:.55rem .9rem; font-size:.8rem; vertical-align:top; }
  .diff-table td:first-child { font-weight:700; color:#64748b; width:40%; border-right:1px solid #f0f2f7; background:#fafafa; }
  .diff-table td:last-child { color:#0D1A30; word-break:break-all; }
  .diff-table.old-table td:last-child { color:#be123c; }
  .diff-table.new-table td:last-child { color:#15803d; }

  .btn-back { background:#f1f5f9; color:#64748b; border:1.5px solid #e2e8f0; border-radius:8px; padding:.45rem 1.1rem; font-size:.82rem; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:.4rem; }
  .btn-back:hover { background:#e2e8f0; }
</style>

<div class="page-hero">
  <div class="orb"></div>
  <div class="container-fluid" style="position:relative;z-index:2;">
    <h1><i class="fas fa-search mr-2" style="color:#C9960C;font-size:1rem;"></i>Activity Detail</h1>
    <p>Full information for this log entry.</p>
  </div>
</div>

<div class="pull-card">
  <div class="container-fluid" style="padding:0;">

    {{-- ── Meta Info ── --}}
    <div class="detail-card">
      <div class="detail-head">
        <div class="detail-title"><i class="fas fa-info-circle"></i> Log Summary</div>
        <a href="{{ route('activity-logs.index') }}" class="btn-back"><i class="fas fa-arrow-left"></i> Back</a>
      </div>
      <div class="detail-body">

        @php
          $icons = ['login'=>'sign-in-alt','logout'=>'sign-out-alt','created'=>'plus','updated'=>'pen','deleted'=>'trash'];
        @endphp

        <div class="meta-grid">

          <div class="meta-item">
            <div class="meta-label">Action</div>
            <div class="meta-value">
              <span class="ab ab-{{ $log->action_color }}">
                <i class="fas fa-{{ $icons[$log->action] ?? 'circle' }}"></i>
                {{ $log->action_label }}
              </span>
            </div>
          </div>

          <div class="meta-item">
            <div class="meta-label">Performed By</div>
            <div class="meta-value" style="display:flex;align-items:center;gap:.5rem;">
              <div style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,#9A6E00,#C9960C);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.6rem;font-weight:800;flex-shrink:0;">
                {{ strtoupper(substr($log->user_name ?? '?', 0, 1)) }}
              </div>
              {{ $log->user_name ?? '—' }}
            </div>
          </div>

          <div class="meta-item">
            <div class="meta-label">Date & Time</div>
            <div class="meta-value">{{ $log->created_at->format('d M Y, h:i A') }}</div>
            <div style="font-size:.74rem;color:#94a3b8;margin-top:.2rem;">{{ $log->created_at->diffForHumans() }}</div>
          </div>

          @if($log->model_type)
          <div class="meta-item">
            <div class="meta-label">Affected Model</div>
            <div class="meta-value">
              <span style="background:#f0f4ff;color:#4f46e5;border:1px solid #c7d2fe;border-radius:6px;padding:.15rem .55rem;font-size:.78rem;font-weight:700;">
                {{ $log->model_name }}
              </span>
              #{{ $log->model_id }}
            </div>
            @if($log->model_label)
            <div style="font-size:.78rem;color:#64748b;margin-top:.3rem;">{{ $log->model_label }}</div>
            @endif
          </div>
          @endif

          <div class="meta-item" style="grid-column:span 2;">
            <div class="meta-label">Description</div>
            <div class="meta-value" style="font-weight:400;color:#475569;">{{ $log->description ?? '—' }}</div>
          </div>

        </div>
      </div>
    </div>

    {{-- ── Old vs New Values (for created/updated/deleted) ── --}}
    @if($log->old_values || $log->new_values)
    <div class="detail-card">
      <div class="detail-head">
        <div class="detail-title"><i class="fas fa-exchange-alt"></i> Data Changes</div>
      </div>
      <div class="detail-body">
        <div class="diff-grid">

          {{-- Old values --}}
          @if($log->old_values)
          <div class="diff-panel">
            <div class="diff-panel-head old"><i class="fas fa-minus-circle"></i> Before</div>
            <table class="diff-table old-table">
              @foreach($log->old_values as $key => $val)
              <tr>
                <td>{{ $key }}</td>
                <td>{{ is_array($val) ? json_encode($val) : ($val ?? '—') }}</td>
              </tr>
              @endforeach
            </table>
          </div>
          @endif

          {{-- New values --}}
          @if($log->new_values)
          <div class="diff-panel">
            <div class="diff-panel-head new"><i class="fas fa-plus-circle"></i> After</div>
            <table class="diff-table new-table">
              @foreach($log->new_values as $key => $val)
              <tr>
                <td>{{ $key }}</td>
                <td>{{ is_array($val) ? json_encode($val) : ($val ?? '—') }}</td>
              </tr>
              @endforeach
            </table>
          </div>
          @endif

        </div>
      </div>
    </div>
    @endif

  </div>
</div>
<div style="height:2rem;"></div>
@endsection