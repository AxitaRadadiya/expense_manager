@extends('admin.layouts.app')
@section('title', 'Dashboard')

@section('content')

<div class="content-header">
  <div class="container-fluid">
    @if(auth()->user() && auth()->user()->hasRole('super-admin'))
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">
          <i class="fas fa-tachometer-alt mr-2 text-teal"></i>Dashboard
        </h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item active">
            <i class="far fa-calendar-alt mr-1"></i>{{ date('l, d M Y') }}
          </li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    {{-- ── STAT CARDS ── --}}
    <div class="row">
      {{-- Total Users --}}
      <div class="col-12 col-sm-6 col-xl-3 mb-3">
        <div class="info-box shadow-sm">
          <span class="info-box-icon bg-teal"><i class="fas fa-users"></i></span>
          <div class="info-box-content">
            <span class="info-box-text text-uppercase font-weight-bold">Total Users</span>
            <span class="info-box-number">{{ $totalUsers ?? 0 }}</span>
            <span class="progress-description text-muted">Active accounts</span>
          </div>
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

    <!-- {{-- Total Expenses --}}
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
    </div> -->

    <!-- {{-- Remaining Balance --}}
    <div class="col-12 col-sm-6 col-xl-3 mb-3">
      @php $remaining = ($totalTransferred ?? 0); @endphp
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
      {{-- Total Transferred --}}
      <div class="col-12 col-sm-6 col-xl-3 mb-3">
        <div class="info-box shadow-sm">
          <span class="info-box-icon bg-success"><i class="fas fa-exchange-alt"></i></span>
          <div class="info-box-content">
            <span class="info-box-text text-uppercase font-weight-bold">Total Transferred</span>
            <span class="info-box-number">₹{{ number_format($totalTransferred ?? 0, 0) }}</span>
            <span class="progress-description text-muted">Across all users</span>
          </div>
        </div>
      </div>
  </div><!-- /.row super-admin cards -->

  @else
  {{-- ════════════════════════════════════════
       REGULAR USER: 4 personalised cards
  ════════════════════════════════════════ --}}
  <div class="row g-3">

    <!-- {{-- Card 1: My Transfer Count --}}
    <div class="col-12 col-sm-6 col-xl-3 mb-3">
      <div class="scard">
        <div class="scard-stripe" style="background:linear-gradient(180deg,#006666,#00b5b5);"></div>
        <div class="scard-icon" style="background:#e0f7f7;">
          <i class="fas fa-list-alt" style="color:#008d8d;"></i>
        </div>
        <div>
          <div class="scard-label">My Transfers</div>
          <div class="scard-num">{{ $userCreatedTransferCount ?? 0 }}</div>
          <div class="scard-sub" style="color:#008d8d;">Created by you</div>
        </div>
        <i class="fas fa-list-alt scard-watermark"></i>
      </div>
    </div> -->

    <!-- {{-- Card 2: Amount Transferred Out --}}
    <div class="col-12 col-sm-6 col-xl-3 mb-3">
      <div class="scard">
        <div class="scard-stripe" style="background:linear-gradient(180deg,#1e8449,#27ae60);"></div>
        <div class="scard-icon" style="background:#e8f8f0;">
          <i class="fas fa-paper-plane" style="color:#27ae60;"></i>
        </div>
        <div>
          <div class="scard-label">Transfers Received</div>
          <div class="scard-num" style="color:#1e8449;">₹{{ number_format($totalTransferred ?? 0, 0) }}</div>
          <div class="scard-sub" style="color:#1e8449;">Incoming transfer total</div>
        </div>
        <i class="fas fa-paper-plane scard-watermark"></i>
      </div>
    </div> -->

    {{-- Card 3: Amount Received / Allocated --}}
    <div class="col-12 col-sm-6 col-xl-3 mb-3">
      <div class="scard">
        <div class="scard-stripe" style="background:linear-gradient(180deg,#5b21b6,#7c3aed);"></div>
        <div class="scard-icon" style="background:#f5f3ff;">
          <i class="fas fa-hand-holding-usd" style="color:#7c3aed;"></i>
        </div>
        <div>
          <div class="scard-label">Total Available</div>
          <div class="scard-num" style="color:#5b21b6;">₹{{ number_format($userReceivedAmount ?? 0, 0) }}</div>
          <div class="scard-sub" style="color:#7c3aed;">Remaining balance</div>
        </div>
        <i class="fas fa-hand-holding-usd scard-watermark"></i>
        </div>
      </div>

    <!-- {{-- Card 4: Transfer Amount --}}
    <div class="col-12 col-sm-6 col-xl-3 mb-3">
      <div class="scard">
        <div class="scard-stripe" style="background:linear-gradient(180deg,#b45309,#f59e0b);"></div>
        <div class="scard-icon" style="background:#fffbeb;">
          <i class="fas fa-arrow-up" style="color:#b45309;"></i>
        </div>
      </div>
    </div> -->


      {{-- Total Expenses --}}
      <div class="col-12 col-sm-6 col-xl-3 mb-3">
        <div class="info-box shadow-sm">
          <span class="info-box-icon bg-danger"><i class="fas fa-receipt"></i></span>
          <div class="info-box-content">
            <span class="info-box-text text-uppercase font-weight-bold">Total Expenses</span>
            <span class="info-box-number">₹{{ number_format($totalExpenses ?? 0, 0) }}</span>
            <span class="progress-description text-muted">Total debited</span>
          </div>
        </div>
      </div>

      {{-- Pending Expenses --}}
      <div class="col-12 col-sm-6 col-xl-3 mb-3">
        <div class="info-box shadow-sm">
          <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
          <div class="info-box-content">
            <span class="info-box-text text-uppercase font-weight-bold">Pending Expenses</span>
            <span class="info-box-number">{{ $pendingExpenses ?? 0 }}</span>
            <span class="progress-description text-muted">Awaiting approval</span>
          </div>
        </div>
      </div>

    </div>{{-- /.row stat cards --}}

    {{-- ── PANELS ROW ── --}}
    <div class="row">

      {{-- User Transfers --}}
      <div class="col-12 col-lg-4 mb-4">
        <div class="card card-teal card-outline shadow-sm h-100">
          <div class="card-header">
            <h3 class="card-title">
              <i class="fas fa-exchange-alt mr-2 text-teal"></i>User Transfers
            </h3>
            <div class="card-tools">
              <span class="badge badge-teal">{{ count($usersWithTransfers) }}</span>
            </div>
          </div>
          <div class="card-body p-0" style="max-height:380px;overflow-y:auto;">
            @forelse($usersWithTransfers as $u)
              <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                <div class="d-flex align-items-center">
                  <span class="info-box-icon bg-teal d-flex align-items-center justify-content-center rounded-circle mr-2"
                        style="width:34px;height:34px;min-width:34px;font-size:.75rem;">
                    {{ strtoupper(substr($u->name, 0, 1)) }}
                  </span>
                  <div>
                    <div class="font-weight-bold" style="font-size:.87rem;">{{ $u->name }}</div>
                    <div class="text-muted" style="font-size:.75rem;">{{ $u->email ?? '—' }}</div>
                  </div>
                </div>
                <span class="badge badge-success badge-pill" style="font-size:.82rem;">
                  ₹{{ number_format((float)($u->transfers_sum_amount ?? 0), 0) }}
                </span>
              </div>
            @empty
              <div class="text-center text-muted py-4">
                <i class="fas fa-exchange-alt fa-2x mb-2 d-block opacity-50"></i>
                No transfers found.
              </div>
            @endforelse
          </div>
        </div>
      </div>

      {{-- Debited Totals --}}
      <div class="col-12 col-lg-8 mb-4">
        <div class="card card-teal card-outline shadow-sm h-100">
          <div class="card-header">
            <h3 class="card-title">
              <i class="fas fa-money-bill-wave mr-2 text-teal"></i>Debited (Expenses)
            </h3>
          </div>
          <div class="card-body">
            @if(auth()->user() && auth()->user()->hasRole('super-admin'))
              {{-- User-wise Debited --}}
              <p class="text-uppercase text-muted font-weight-bold mb-2" style="font-size:.7rem;letter-spacing:1px;">
                <i class="fas fa-users mr-1"></i> User-wise Debited Totals
              </p>
              <div class="table-responsive mb-4">
                <table class="table table-sm table-hover table-bordered">
                  <thead class="thead-dark">
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
                        <td class="text-muted">{{ $i + 1 }}</td>
                        <td>
                          <span class="font-weight-bold">{{ $u->user->name ?? '—' }}</span>
                        </td>
                        <td class="text-right">
                          <span class="badge badge-danger">₹{{ number_format((float)($u->total_debited ?? 0), 0) }}</span>
                        </td>
                        <td class="text-right text-muted">{{ $u->expenses_count }}</td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="4" class="text-center text-muted py-3">No data found.</td>
                      </tr>
                    @endforelse
                  </tbody>
                  @if($userDebitedTotals->count())
                    <tfoot class="bg-light">
                      <tr>
                        <td colspan="2" class="text-right font-weight-bold">Total</td>
                        <td class="text-right font-weight-bold text-danger">₹{{ number_format($userDebitedTotals->sum('total_debited'), 0) }}</td>
                        <td class="text-right font-weight-bold">{{ $userDebitedTotals->sum('expenses_count') }}</td>
                      </tr>
                    </tfoot>
                  @endif
                </table>
              </div>

              {{-- Project-wise Debited --}}
              <p class="text-uppercase text-muted font-weight-bold mb-2" style="font-size:.7rem;letter-spacing:1px;">
                <i class="fas fa-folder-open mr-1"></i> Project-wise Debited Totals
              </p>
              <div class="table-responsive">
                <table class="table table-sm table-hover table-bordered">
                  <thead class="thead-dark">
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
                        <td class="text-muted">{{ $i + 1 }}</td>
                        <td>
                          <span class="badge badge-info">{{ $p->project->name ?? '—' }}</span>
                        </td>
                        <td class="text-right">
                          <span class="badge badge-danger">₹{{ number_format((float)($p->total_debited ?? 0), 0) }}</span>
                        </td>
                        <td class="text-right text-muted">{{ $p->expenses_count }}</td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="4" class="text-center text-muted py-3">No data found.</td>
                      </tr>
                    @endforelse
                  </tbody>
                  @if($projectDebitedTotals->count())
                    <tfoot class="bg-light">
                      <tr>
                        <td colspan="2" class="text-right font-weight-bold">Total</td>
                        <td class="text-right font-weight-bold text-danger">₹{{ number_format($projectDebitedTotals->sum('total_debited'), 0) }}</td>
                        <td class="text-right font-weight-bold">{{ $projectDebitedTotals->sum('expenses_count') }}</td>
                      </tr>
                    </tfoot>
                  @endif
                </table>
              </div>
            @else
              {{-- Regular user: recent debits --}}
              <p class="text-uppercase text-muted font-weight-bold mb-2" style="font-size:.7rem;letter-spacing:1px;">
                <i class="fas fa-receipt mr-1"></i> Your Recent Debits
              </p>
              <div class="table-responsive">
                <table class="table table-sm table-hover table-bordered">
                  <thead class="thead-dark">
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
                        <td class="text-muted text-nowrap">
                          {{ $d->expense_date ? \Carbon\Carbon::parse($d->expense_date)->format('d M Y') : '—' }}
                        </td>
                        <td>
                          <span class="badge badge-info">{{ $d->project->name ?? '—' }}</span>
                        </td>
                        <td class="text-right">
                          <span class="badge badge-danger">₹{{ number_format((float)$d->amount, 0) }}</span>
                        </td>
                        <td class="text-muted" style="font-size:.82rem;">
                          {{ \Illuminate\Support\Str::limit($d->description ?? '—', 80) }}
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="4" class="text-center text-muted py-3">No expenses found.</td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>

            @endif

          </div>
        </div>
      </div>
    </div>{{-- /.row panels --}}
  </div>
</section>
  @endif
</section>
@endsection
