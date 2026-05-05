@extends('admin.layouts.app')
@section('title', 'Dashboard')

@section('content')

{{-- Page Header --}}
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">
          <i class="fas fa-tachometer-alt mr-2 text-teal"></i>Dashboard
        </h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item active">
            <i class="far fa-calendar-alt mr-1"></i>{{ date('l, d-m-Y') }}
          </li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">

    {{-- Stat Cards --}}
    <div class="row">

      {{-- Available Amount --}}
      <div class="col-12 col-sm-6 col-xl-3 mb-3">
        <div class="info-box shadow-sm">
          <span class="info-box-icon bg-primary"><i class="fas fa-wallet"></i></span>
          <div class="info-box-content">
            <span class="info-box-text text-uppercase font-weight-bold">Available Amount</span>
            <span class="info-box-number">&#8377;{{ number_format((float) ($availableAmount ?? 0), 2) }}</span>
            <span class="progress-description text-muted">Current balance</span>
          </div>
        </div>
      </div>

      <!-- <div class="col-12 col-sm-6 col-xl-3 mb-3">
        <div class="info-box shadow-sm">
          <span class="info-box-icon bg-success"><i class="fas fa-coins"></i></span>
          <div class="info-box-content">
            <span class="info-box-text text-uppercase font-weight-bold">Credit Amount</span>
            <span class="info-box-number">&#8377;{{ number_format((float) ($totalCredits ?? 0), 2) }}</span>
            <span class="progress-description text-muted">{{ $isSuper ? 'Total credited amount' : 'Your total credited amount' }}</span>
          </div>
        </div>
      </div> -->

      @if($isSuper)

      <!-- {{-- Total Transferred --}}
      <div class="col-12 col-sm-6 col-xl-3 mb-3">
        <div class="info-box shadow-sm">
          <span class="info-box-icon bg-success"><i class="fas fa-exchange-alt"></i></span>
          <div class="info-box-content">
            <span class="info-box-text text-uppercase font-weight-bold">Total Transferred</span>
            <span class="info-box-number">&#8377;{{ number_format((float) ($totalTransferred ?? 0), 2) }}</span>
            <span class="progress-description text-muted">Across all users</span>
          </div>
        </div>
      </div> -->

      {{-- Total Expenses --}}
      <!-- <div class="col-12 col-sm-6 col-xl-3 mb-3">
        <div class="info-box shadow-sm">
          <span class="info-box-icon bg-danger"><i class="fas fa-receipt"></i></span>
          <div class="info-box-content">
            <span class="info-box-text text-uppercase font-weight-bold">Total Expenses</span>
            <span class="info-box-number">&#8377;{{ number_format((float) ($totalExpenses ?? 0), 2) }}</span>
            <span class="progress-description text-muted">Total debited</span>
          </div>
        </div>
      </div> -->

      @endif
    </div>

    <div class="row">

      @if($isSuper)
      <div class="col-12 col-lg-4 mb-4">
        <div class="card card-teal card-outline shadow-sm h-100">
          <div class="card-header">
            <h3 class="card-title">
              <i class="fas fa-wallet mr-2 text-teal"></i>User Balances
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
                    <div class="font" style="font-size:.87rem;">{{ $u->name }}</div>
                    <div class="text-muted" style="font-size:.75rem;">Current amount</div>
                  </div>
                </div>
                <span class="badge badge-success badge-pill" style="font-size:.82rem;">
                  &#8377;{{ number_format((float) ($u->amount ?? 0), 2) }}
                </span>
              </div>
            @empty
              <div class="text-center text-muted py-4">
                <i class="fas fa-users fa-2x mb-2 d-block opacity-50"></i>
                No users found.
              </div>
            @endforelse
          </div>
        </div>
      </div>
      @endif

      <div class="col-12 col-lg-8 mb-4">
        <div class="card card-teal card-outline shadow-sm h-100">
          <div class="card-header">
            <h3 class="card-title">
              <i class="fas fa-money-bill-wave mr-2 text-teal"></i>Debited (Expenses)
            </h3>
          </div>
          <div class="card-body">

            @if($isSuper)

              <p class="text-uppercase text-muted font-weight-bold mb-2" style="font-size:.7rem;letter-spacing:1px;">
                <i class="fas fa-users mr-1"></i> User-wise Debited Totals
              </p>
              <div class="table-responsive mb-4">
                <table class="table table-sm table-hover table-bordered">
                  <thead class="thead">
                    <tr>
                      <th>#</th>
                      <th>User</th>
                      <th class="text-right">Total Debited</th>
                      <!-- <th class="text-right">Count</th> -->
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($userDebitedTotals as $i => $u)
                      <tr>
                        <td class="text-muted">{{ $i + 1 }}</td>
                        <td>
                          <span class="font">{{ $u->user->name ?? '-' }}</span>
                        </td>
                        <td class="text-right">
                          <span class="badge badge-danger">&#8377;{{ number_format((float) ($u->total_debited ?? 0), 2) }}</span>
                        </td>
                        <!-- <td class="text-right text-muted">{{ $u->expenses_count }}</td> -->
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
                        <td class="text-right font-weight-bold text-danger">&#8377;{{ number_format((float) $userDebitedTotals->sum('total_debited'), 2) }}</td>
                        <!-- <td class="text-right font-weight-bold">{{ $userDebitedTotals->sum('expenses_count') }}</td> -->
                      </tr>
                    </tfoot>
                  @endif
                </table>
              </div>

              <p class="text-uppercase text-muted font-weight-bold mb-2" style="font-size:.7rem;letter-spacing:1px;">
                <i class="fas fa-folder-open mr-1"></i> Project-wise Debited Totals
              </p>
              <div class="table-responsive">
                <table class="table table-sm table-hover table-bordered">
                  <thead class="thead">
                    <tr>
                      <th>#</th>
                      <th>Project</th>
                      <th class="text-right">Total Debited</th>
                      <!-- <th class="text-right">Count</th> -->
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($projectDebitedTotals as $i => $p)
                      <tr>
                        <td class="text-muted">{{ $i + 1 }}</td>
                        <td>
                          <span class="badge badge-info">{{ $p->project->name ?? '-' }}</span>
                        </td>
                        <td class="text-right">
                          <span class="badge badge-danger">&#8377;{{ number_format((float) ($p->total_debited ?? 0), 2) }}</span>
                        </td>
                        <!-- <td class="text-right text-muted">{{ $p->expenses_count }}</td> -->
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
                        <td class="text-right font-weight-bold text-danger">&#8377;{{ number_format((float) $projectDebitedTotals->sum('total_debited'), 2) }}</td>
                        <!-- <td class="text-right font-weight-bold">{{ $projectDebitedTotals->sum('expenses_count') }}</td> -->
                      </tr>
                    </tfoot>
                  @endif
                </table>
              </div>

            @else

              <p class="text-uppercase text-muted font-weight-bold mb-2" style="font-size:.7rem;letter-spacing:1px;">
                <i class="fas fa-receipt mr-1"></i> Your Recent Debits
              </p>
              <div class="table-responsive">
                <table class="table table-sm table-hover table-bordered">
                  <thead class="thead">
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
                          {{ $d->expense_date ? \Carbon\Carbon::parse($d->expense_date)->format('d-m-Y') : '-' }}
                        </td>
                        <td>
                          <span class="badge badge-info">{{ $d->project->name ?? '-' }}</span>
                        </td>
                        <td class="text-right">
                          <span class="badge badge-danger">&#8377;{{ number_format((float) $d->amount, 2) }}</span>
                        </td>
                        <td class="text-muted" style="font-size:.82rem;">
                          {{ \Illuminate\Support\Str::limit($d->description ?? '-', 80) }}
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

    </div>

    @if($isSuper || auth()->user()?->hasRole('owner'))
    <div class="row">
      <div class="col-12 mb-4">
        <div class="card card-success card-outline shadow-sm">
          <div class="card-header">
            <h3 class="card-title">
              <i class="fas fa-coins mr-2 text-success"></i>{{ $isSuper ? 'Project-wise Credits' : 'Your Project-wise Credits' }}
            </h3>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-sm table-hover table-bordered">
                <thead class="thead">
                  <tr>
                    <th>#</th>
                    <th>Project</th>
                    <th class="text-right">Total Credit</th>
                    <!-- <th class="text-right">Count</th> -->
                  </tr>
                </thead>
                <tbody>
                  @forelse($projectCreditTotals as $i => $credit)
                    <tr>
                      <td class="text-muted">{{ $i + 1 }}</td>
                      <td>
                        <span class="badge badge-info">{{ $credit->project->name ?? '-' }}</span>
                      </td>
                      <td class="text-right">
                        <span class="badge badge-success">&#8377;{{ number_format((float) ($credit->total_credited ?? 0), 2) }}</span>
                      </td>
                      <!-- <td class="text-right text-muted">{{ $credit->credits_count }}</td> -->
                    </tr>
                  @empty
                    <tr>
                      <td colspan="4" class="text-center text-muted py-3">No credit data found.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endif

  </div>
</section>
@endsection
