@extends('admin.layouts.app')
@section('title', 'Dashboard')

@section('content')

<style>
  .content-wrapper { background: #f0f6f6 !important; }

  /* ══ HERO ══ */
  .dash-hero {
    background: linear-gradient(135deg, #006666 0%, #008d8d 55%, #00a8a8 100%);
    padding: 2.2rem 2rem 5rem;
    position: relative; overflow: hidden;
  }
  .dash-hero::before {
    content: ''; position: absolute; inset: 0; pointer-events: none;
    background-image: radial-gradient(rgba(255,255,255,.08) 1px, transparent 1px);
    background-size: 28px 28px;
  }
  .dash-hero .orb { position: absolute; border-radius: 50%; pointer-events: none; }
  .orb-a { width:260px; height:260px; background: radial-gradient(circle, rgba(255,255,255,.14) 0%, transparent 65%); top:-80px; right:60px; }
  .orb-b { width:180px; height:180px; background: radial-gradient(circle, rgba(255,255,255,.08) 0%, transparent 70%); bottom:0; right:320px; }
  .orb-c { width:100px; height:100px; background: radial-gradient(circle, rgba(255,255,255,.1)  0%, transparent 70%); top:30px; left:38%; }

  .dash-hero h1 {
    font-family: 'Playfair Display', serif;
    font-size: 1.65rem; font-weight: 800; color: #fff; margin: 0 0 .3rem;
  }
  .dash-hero p { color: rgba(255,255,255,.65); font-size: .87rem; margin: 0; }
  .dash-date-pill {
    background: rgba(255,255,255,.15);
    border: 1px solid rgba(255,255,255,.3);
    color: #fff; border-radius: 20px; padding: .4rem 1.1rem;
    font-size: .78rem; font-weight: 700;
    display: inline-flex; align-items: center; gap: .45rem;
  }

  /* ══ STAT CARDS ══ */
  .dash-stats { margin-top: -3rem; position: relative; z-index: 10; padding: 0 1.5rem; }

  .scard {
    background: #fff; border-radius: 16px;
    padding: 1.4rem 1.5rem;
    box-shadow: 0 6px 28px rgba(0,141,141,.12);
    display: flex; align-items: center; gap: 1.1rem;
    height: 100%; position: relative; overflow: hidden;
    transition: transform .22s, box-shadow .22s;
    border: 1px solid #d0eded;
  }
  .scard:hover { transform: translateY(-5px); box-shadow: 0 14px 40px rgba(0,141,141,.2); }
  .scard-stripe { position:absolute; top:0; left:0; width:4px; height:100%; border-radius:16px 0 0 16px; }
  .scard-watermark { position:absolute; bottom:-8px; right:-4px; font-size:4.5rem; opacity:.04; line-height:1; }
  .scard-icon { width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:1.2rem; }
  .scard-num   { font-size:2.1rem; font-weight:800; line-height:1; color:#0d2e2e; margin-bottom:.1rem; }
  .scard-label { font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#5a8080; margin-bottom:.2rem; }
  .scard-sub   { font-size:.74rem; font-weight:600; color:#2a5050; }

  /* ══ SECTION HEADING ══ */
  .sh {
    font-size:.68rem; font-weight:800; text-transform:uppercase; letter-spacing:1.8px;
    color:#5a8080; margin:0 0 1rem;
    display:flex; align-items:center; gap:.6rem;
  }
  .sh .sh-icon {
    width:22px; height:22px;
    background: linear-gradient(135deg,#006666,#008d8d);
    border-radius:6px;
    display:inline-flex; align-items:center; justify-content:center;
    font-size:.62rem; color:#fff;
  }
  .sh::after { content:''; flex:1; height:1px; background:#d0eded; }

  /* ══ PANEL ══ */
  .panel {
    background:#fff; border-radius:16px;
    box-shadow: 0 4px 24px rgba(0,141,141,.10);
    overflow:hidden; height:100%;
    border:1px solid #d0eded;
  }
  .panel-head {
    padding:1.1rem 1.5rem; border-bottom:1px solid #e4f0f0;
    display:flex; align-items:center; justify-content:space-between;
    background:#f9fdfd;
  }
  .panel-title { font-size:.92rem; font-weight:800; color:#0d2e2e; display:flex; align-items:center; gap:.5rem; }
  .panel-title i { color:#008d8d; }
  .panel-body { padding:1.3rem 1.5rem; }

  /* ══ TRANSFER LIST ══ */
  .transfer-item {
    display:flex; align-items:center; justify-content:space-between;
    padding:.75rem 1rem; border-radius:10px;
    border:1px solid #e4f0f0; margin-bottom:.6rem;
    background:#f9fdfd; transition:background .15s;
  }
  .transfer-item:hover { background:#e0f7f7; }
  .transfer-item:last-child { margin-bottom:0; }
  .t-avatar {
    width:34px; height:34px; border-radius:50%;
    background:linear-gradient(135deg,#006666,#008d8d);
    display:flex; align-items:center; justify-content:center;
    color:#fff; font-size:.7rem; font-weight:800; flex-shrink:0;
  }
  .t-name  { font-size:.85rem; font-weight:700; color:#0d2e2e; }
  .t-email { font-size:.73rem; color:#5a8080; }
  .t-amt   { font-size:.95rem; font-weight:800; color:#006666; }

  /* ══ TABLES ══ */
  .dash-table { width:100%; border-collapse:collapse; }
  .dash-table thead th {
    background:#008d8d; color:#fff;
    font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.7px;
    padding:.65rem .9rem; white-space:nowrap;
    border-bottom:2px solid #006666;
  }
  .dash-table tbody tr { border-bottom:1px solid #e4f0f0; transition:background .15s; }
  .dash-table tbody tr:hover { background:#e0f7f7; }
  .dash-table tbody td { padding:.65rem .9rem; font-size:.82rem; color:#0d2e2e; vertical-align:middle; }
  .dash-table tbody td.text-right { text-align:right; }
  .dash-table tfoot td { padding:.65rem .9rem; font-size:.82rem; font-weight:800; color:#006666; background:#e0f7f7; border-top:2px solid #a0d8d8; }

  /* Amount chip */
  .amt-chip {
    background:#e0f7f7; color:#006666;
    border:1px solid #a0d8d8; border-radius:6px;
    padding:.15rem .65rem; font-size:.78rem; font-weight:700;
    display:inline-block; white-space:nowrap;
  }
  .amt-chip.red { background:#fff1f2; color:#be123c; border-color:#fecdd3; }
</style>

{{-- ── HERO ── --}}
<div class="dash-hero">
  <div class="orb orb-a"></div>
  <div class="orb orb-b"></div>
  <div class="orb orb-c"></div>
  <div class="container-fluid" style="position:relative;z-index:2;">
    <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap:1rem;">
      <div>
        <h1>Good day, {{ ucfirst(auth()->user()->name) }} ✦</h1>
        <p>Here's a live overview of your expense operations.</p>
      </div>
      <div class="dash-date-pill">
        <i class="far fa-calendar-alt"></i> {{ date('l, d M Y') }}
      </div>
    </div>
  </div>
</div>

{{-- ── STAT CARDS ── --}}
<div class="dash-stats">
  <div class="row g-3">

    {{-- Total Users --}}
    <div class="col-12 col-sm-6 col-xl-3 mb-3">
      <div class="scard">
        <div class="scard-stripe" style="background:linear-gradient(180deg,#006666,#00b5b5);"></div>
        <div class="scard-icon" style="background:#e0f7f7;">
          <i class="fas fa-users" style="color:#008d8d;"></i>
        </div>
        <div>
          <div class="scard-label">Total Users</div>
          <div class="scard-num">{{ $totalUsers ?? 0 }}</div>
          <div class="scard-sub" style="color:#008d8d;">Active accounts</div>
        </div>
        <i class="fas fa-users scard-watermark"></i>
      </div>
    </div>

    {{-- Total Transfers --}}
    <div class="col-12 col-sm-6 col-xl-3 mb-3">
      <div class="scard">
        <div class="scard-stripe" style="background:linear-gradient(180deg,#1e8449,#27ae60);"></div>
        <div class="scard-icon" style="background:#e8f8f0;">
          <i class="fas fa-exchange-alt" style="color:#27ae60;"></i>
        </div>
        <div>
          <div class="scard-label">Total Transferred</div>
          <div class="scard-num" style="color:#1e8449;">₹{{ number_format($totalTransferred ?? 0, 0) }}</div>
          <div class="scard-sub" style="color:#1e8449;">Across all users</div>
        </div>
        <i class="fas fa-exchange-alt scard-watermark"></i>
      </div>
    </div>

    {{-- Total Expenses --}}
    <div class="col-12 col-sm-6 col-xl-3 mb-3">
      <div class="scard">
        <div class="scard-stripe" style="background:linear-gradient(180deg,#be123c,#f43f5e);"></div>
        <div class="scard-icon" style="background:#fff1f2;">
          <i class="fas fa-receipt" style="color:#be123c;"></i>
        </div>
        <div>
          <div class="scard-label">Total Expenses</div>
          <div class="scard-num" style="color:#be123c;">₹{{ number_format($totalExpenses ?? 0, 0) }}</div>
          <div class="scard-sub" style="color:#be123c;">Total debited</div>
        </div>
        <i class="fas fa-receipt scard-watermark"></i>
      </div>
    </div>

    {{-- Remaining Balance --}}
    <div class="col-12 col-sm-6 col-xl-3 mb-3">
      @php $remaining = ($totalTransferred ?? 0) - ($totalExpenses ?? 0); @endphp
      <div class="scard">
        <div class="scard-stripe" style="background:linear-gradient(180deg,#b7770d,#f59e0b);"></div>
        <div class="scard-icon" style="background:#fef9ec;">
          <i class="fas fa-wallet" style="color:#b7770d;"></i>
        </div>
        <div>
          <div class="scard-label">Remaining Balance</div>
          <div class="scard-num" style="color:{{ $remaining >= 0 ? '#006666' : '#be123c' }};">
            ₹{{ number_format(abs($remaining), 0) }}
          </div>
          <div class="scard-sub" style="color:{{ $remaining >= 0 ? '#008d8d' : '#be123c' }};">
            {{ $remaining >= 0 ? 'Available' : 'Overspent' }}
          </div>
        </div>
        <i class="fas fa-wallet scard-watermark"></i>
      </div>
    </div>

  </div><!-- /.row -->

  {{-- ── PANELS ── --}}
  <div class="row mt-2">

    {{-- User Transfers --}}
    <div class="col-12 col-lg-4 mb-4">
      <div class="panel">
        <div class="panel-head">
          <div class="panel-title"><i class="fas fa-exchange-alt"></i> User Transfers</div>
          <span style="background:#e0f7f7;color:#006666;border:1px solid #a0d8d8;border-radius:6px;padding:.1rem .6rem;font-size:.72rem;font-weight:800;">
            {{ count($usersWithTransfers) }}
          </span>
        </div>
        <div class="panel-body" style="max-height:380px;overflow-y:auto;">
          @forelse($usersWithTransfers as $u)
          <div class="transfer-item">
            <div style="display:flex;align-items:center;gap:.7rem;">
              <div class="t-avatar">{{ strtoupper(substr($u->name, 0, 1)) }}</div>
              <div>
                <div class="t-name">{{ $u->name }}</div>
                <div class="t-email">{{ $u->email ?? '—' }}</div>
              </div>
            </div>
            <div class="t-amt">₹{{ number_format((float)($u->transfers_sum_amount ?? 0), 0) }}</div>
          </div>
          @empty
          <div style="text-align:center;padding:2rem;color:#5a8080;font-size:.83rem;">
            <i class="fas fa-exchange-alt" style="font-size:1.5rem;display:block;margin-bottom:.5rem;opacity:.3;"></i>
            No transfers found.
          </div>
          @endforelse
        </div>
      </div>
    </div>

    {{-- Debited Totals --}}
    <div class="col-12 col-lg-8 mb-4">
      <div class="panel">
        <div class="panel-head">
          <div class="panel-title"><i class="fas fa-money-bill-wave"></i> Debited (Expenses)</div>
        </div>
        <div class="panel-body">

          @if(auth()->user() && auth()->user()->hasRole('super-admin'))

            {{-- User-wise --}}
            <div class="sh mb-2">
              <span class="sh-icon"><i class="fas fa-users" style="font-size:.55rem;"></i></span>
              User-wise Debited Totals
            </div>
            <div class="table-responsive mb-4">
              <table class="dash-table">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>User</th>
                    <th class="text-right">Total Debited</th>
                    <th class="text-right">Count</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($userDebitedTotals as $i => $u)
                  <tr>
                    <td style="color:#5a8080;font-size:.76rem;">{{ $i + 1 }}</td>
                    <td>
                      <div style="display:flex;align-items:center;gap:.5rem;">
                        <div style="width:26px;height:26px;border-radius:50%;background:linear-gradient(135deg,#006666,#008d8d);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.58rem;font-weight:800;flex-shrink:0;">
                          {{ strtoupper(substr($u->user->name ?? '?', 0, 1)) }}
                        </div>
                        <span style="font-weight:600;">{{ $u->user->name ?? '—' }}</span>
                      </div>
                    </td>
                    <td class="text-right">
                      <span class="amt-chip red">₹{{ number_format((float)($u->total_debited ?? 0), 0) }}</span>
                    </td>
                    <td class="text-right" style="color:#5a8080;">{{ $u->expenses_count }}</td>
                  </tr>
                  @empty
                  <tr><td colspan="4" style="text-align:center;color:#5a8080;padding:1.5rem;">No data found.</td></tr>
                  @endforelse
                </tbody>
                @if($userDebitedTotals->count())
                <tfoot>
                  <tr>
                    <td colspan="2" style="text-align:right;">Total</td>
                    <td class="text-right">₹{{ number_format($userDebitedTotals->sum('total_debited'), 0) }}</td>
                    <td class="text-right">{{ $userDebitedTotals->sum('expenses_count') }}</td>
                  </tr>
                </tfoot>
                @endif
              </table>
            </div>

            {{-- Project-wise --}}
            <div class="sh mb-2">
              <span class="sh-icon"><i class="fas fa-folder-open" style="font-size:.55rem;"></i></span>
              Project-wise Debited Totals
            </div>
            <div class="table-responsive">
              <table class="dash-table">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Project</th>
                    <th class="text-right">Total Debited</th>
                    <th class="text-right">Count</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($projectDebitedTotals as $i => $p)
                  <tr>
                    <td style="color:#5a8080;font-size:.76rem;">{{ $i + 1 }}</td>
                    <td>
                      <span style="background:#e0f7f7;color:#006666;border:1px solid #a0d8d8;border-radius:6px;padding:.15rem .55rem;font-size:.78rem;font-weight:700;">
                        {{ $p->project->name ?? '—' }}
                      </span>
                    </td>
                    <td class="text-right">
                      <span class="amt-chip red">₹{{ number_format((float)($p->total_debited ?? 0), 0) }}</span>
                    </td>
                    <td class="text-right" style="color:#5a8080;">{{ $p->expenses_count }}</td>
                  </tr>
                  @empty
                  <tr><td colspan="4" style="text-align:center;color:#5a8080;padding:1.5rem;">No data found.</td></tr>
                  @endforelse
                </tbody>
                @if($projectDebitedTotals->count())
                <tfoot>
                  <tr>
                    <td colspan="2" style="text-align:right;">Total</td>
                    <td class="text-right">₹{{ number_format($projectDebitedTotals->sum('total_debited'), 0) }}</td>
                    <td class="text-right">{{ $projectDebitedTotals->sum('expenses_count') }}</td>
                  </tr>
                </tfoot>
                @endif
              </table>
            </div>

          @else

            {{-- Regular user: recent debits --}}
            <div class="sh mb-2">
              <span class="sh-icon"><i class="fas fa-receipt" style="font-size:.55rem;"></i></span>
              Your Recent Debits
            </div>
            <div class="table-responsive">
              <table class="dash-table">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Project</th>
                    <th class="text-right">Amount</th>
                    <th>Description</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($debitedList as $d)
                  <tr>
                    <td style="white-space:nowrap;color:#5a8080;">
                      {{ $d->expense_date ? \Carbon\Carbon::parse($d->expense_date)->format('d M Y') : '—' }}
                    </td>
                    <td>
                      <span style="background:#e0f7f7;color:#006666;border:1px solid #a0d8d8;border-radius:6px;padding:.1rem .5rem;font-size:.75rem;font-weight:700;">
                        {{ $d->project->name ?? '—' }}
                      </span>
                    </td>
                    <td class="text-right">
                      <span class="amt-chip red">₹{{ number_format((float)$d->amount, 0) }}</span>
                    </td>
                    <td style="color:#5a8080;font-size:.8rem;">
                      {{ \Illuminate\Support\Str::limit($d->description ?? '—', 80) }}
                    </td>
                  </tr>
                  @empty
                  <tr><td colspan="4" style="text-align:center;color:#5a8080;padding:1.5rem;">No expenses found.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>

          @endif

        </div>
      </div>
    </div>

  </div><!-- /.row panels -->

</div><!-- /.dash-stats -->

<div style="height:2rem;"></div>
@endsection