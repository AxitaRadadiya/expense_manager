{{-- resources/views/admin/users/show.blade.php --}}
@extends('admin.layouts.app')
@section('title', 'User Details')
@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><i class="fas fa-user mr-2 text-primary"></i>User Details</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
          <li class="breadcrumb-item active">{{ $user->name }}</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="row">

      {{-- ── Profile Card ── --}}
      <div class="col-lg-3 col-md-4 mb-4">
        <div class="card card-outline card-primary shadow-sm">
          <div class="card-body text-center pt-4">
            <img src="{{ $user->profile_image_url }}"
                 alt="{{ $user->name }}"
                 class="rounded-circle mb-3"
                 style="width:72px;height:72px;object-fit:cover;border:3px solid rgba(0, 141, 141, .15);padding:4px;background:#fff;">
            <h5 class="font-weight-bold mb-1">{{ $user->name }}</h5>
            <p class="text-muted mb-2">{{ $user->email }}</p>
            <span class="badge {{ $user->status ? 'badge-success' : 'badge-danger' }} mb-3">
              {{ $user->status ? 'Active' : 'Inactive' }}
            </span>
            <ul class="list-group list-group-unbordered text-left mt-3">
              <li class="list-group-item">
                <b><i class="fas fa-shield-alt mr-1 text-primary"></i>Role</b>
                <span class="float-right">{{ $user->role->name ?? '—' }}</span>
              </li>
              <li class="list-group-item">
                <b><i class="fas fa-phone mr-1 text-primary"></i>Mobile</b>
                <span class="float-right">{{ $user->mobile ?? '—' }}</span>
              </li>
              <li class="list-group-item">
                <b><i class="fas fa-folder mr-1 text-primary"></i>Project</b>
                <span class="float-right">{{ optional($user->project)->name ?? '—' }}</span>
              </li>
              <li class="list-group-item">
                <b><i class="fas fa-rupee-sign mr-1 text-primary"></i>Opening Balance</b>
                <span class="float-right text-success font-weight-bold">
                  ₹{{ number_format((float)($user->amount ?? 0), 2) }}
                </span>
              </li>
            </ul>
            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary btn-sm btn-block mt-3">
              <i class="fas fa-edit mr-1"></i>Edit User
            </a>
          </div>
        </div>
      </div>

      {{-- ── Summary + Tables ── --}}
      <div class="col-lg-9 col-md-8">

        {{-- Balance Summary --}}
        <div class="row mb-3">
          <div class="col-sm-3">
            <div class="info-box shadow-sm">
              <span class="info-box-icon bg-info"><i class="fas fa-wallet"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Opening Balance</span>
                <span class="info-box-number" style="font-size:1rem;">₹{{ number_format($opening, 2) }}</span>
              </div>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="info-box shadow-sm">
              <span class="info-box-icon bg-success"><i class="fas fa-exchange-alt"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Total Transfers</span>
                <span class="info-box-number" style="font-size:1rem;">₹{{ number_format($totalTransfers ?? 0, 2) }}</span>
              </div>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="info-box shadow-sm">
              <span class="info-box-icon bg-danger"><i class="fas fa-receipt"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Total Debited</span>
                <span class="info-box-number" style="font-size:1rem;">₹{{ number_format($totalDebited ?? 0, 2) }}</span>
              </div>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="info-box shadow-sm">
              <span class="info-box-icon bg-warning"><i class="fas fa-balance-scale"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Current Balance</span>
                <span class="info-box-number" style="font-size:1rem;">₹{{ number_format($currentBalance, 2) }}</span>
              </div>
            </div>
          </div>
        </div>

        {{-- Expenses Table --}}
        <div class="card card-outline card-danger shadow-sm mb-3">
          <div class="card-header">
            <h3 class="card-title">
              <i class="fas fa-receipt mr-2"></i>Debited (Expenses)
              <span class="badge badge-danger ml-1">{{ $expenses->total() }}</span>
            </h3>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-sm table-hover table-bordered mb-0">
                <thead class="thead-dark">
                  <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Project</th>
                    <th class="text-right">Amount</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($expenses as $i => $exp)
                    <tr>
                      <td class="text-muted">{{ $expenses->firstItem() + $i }}</td>
                      <td class="text-nowrap">{{ optional($exp->expense_date)->format('d M Y') ?? '—' }}</td>
                      <td><span class="badge badge-info">{{ optional($exp->project)->name ?? '—' }}</span></td>
                      <td class="text-right"><span class="badge badge-danger">₹{{ number_format((float)$exp->amount, 2) }}</span></td>
                      <td class="text-muted" style="font-size:.82rem;">{{ \Illuminate\Support\Str::limit($exp->description ?? '—', 60) }}</td>
                      <td>
                        @php $sc = match($exp->status ?? '') { 'approved'=>'badge-success','rejected'=>'badge-danger', default=>'badge-warning' }; @endphp
                        <span class="badge {{ $sc }}">{{ ucfirst($exp->status ?? '—') }}</span>
                      </td>
                      <td>
                        <a href="{{ route('expense.show', $exp->id) }}" class="btn btn-xs btn-outline-primary">
                          <i class="fas fa-eye"></i>
                        </a>
                      </td>
                    </tr>
                  @empty
                    <tr><td colspan="7" class="text-center text-muted py-3">No expenses found.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
            @if($expenses->hasPages())
              <div class="card-footer clearfix">{{ $expenses->links('pagination::bootstrap-4') }}</div>
            @endif
          </div>
        </div>

        {{-- Transfers Table --}}
        <div class="card card-outline card-success shadow-sm mb-3">
          <div class="card-header">
            <h3 class="card-title">
              <i class="fas fa-exchange-alt mr-2"></i>Transfers
              <span class="badge badge-success ml-1">{{ $transfers->total() }}</span>
            </h3>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-sm table-hover table-bordered mb-0">
                <thead class="thead-dark">
                  <tr>
                    <th>#</th><th>Date</th>
                    <th class="text-right">Amount</th>
                    <th>Created By</th><th>Note</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($transfers as $i => $t)
                    <tr>
                      <td class="text-muted">{{ $transfers->firstItem() + $i }}</td>
                      <td class="text-nowrap">{{ optional($t->start_date)->format('d M Y') ?? '—' }}</td>
                      <td class="text-right"><span class="badge badge-success">₹{{ number_format((float)$t->amount, 2) }}</span></td>
                      <td>{{ optional($t->creator)->name ?? '—' }}</td>
                      <td class="text-muted" style="font-size:.82rem;">{{ \Illuminate\Support\Str::limit($t->note ?? '—', 60) }}</td>
                    </tr>
                  @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">No transfers found.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
            @if($transfers->hasPages())
              <div class="card-footer clearfix">{{ $transfers->links('pagination::bootstrap-4') }}</div>
            @endif
          </div>
        </div>

        {{-- Balance History Table --}}
        <div class="card card-outline card-info shadow-sm mb-3">
          <div class="card-header">
            <h3 class="card-title">
              <i class="fas fa-history mr-2"></i>Balance History
              <span class="badge badge-info ml-1">{{ $balanceHistories->total() }}</span>
            </h3>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-sm table-hover table-bordered mb-0">
                <thead class="thead-dark">
                  <tr>
                    <th>#</th><th>Date</th><th>Type</th>
                    <th class="text-right">Change</th>
                    <th class="text-right">Before</th>
                    <th class="text-right">After</th>
                    <th>Note</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($balanceHistories as $i => $b)
                    <tr>
                      <td class="text-muted">{{ $balanceHistories->firstItem() + $i }}</td>
                      <td class="text-nowrap" style="font-size:.82rem;">{{ optional($b->created_at)->format('d M Y H:i') ?? '—' }}</td>
                      <td><span class="badge badge-secondary">{{ ucfirst($b->change_type ?? '—') }}</span></td>
                      <td class="text-right">
                        <span class="badge {{ $b->change_amount >= 0 ? 'badge-success' : 'badge-danger' }}">
                          ₹{{ number_format((float)$b->change_amount, 2) }}
                        </span>
                      </td>
                      <td class="text-right text-muted">₹{{ number_format((float)$b->balance_before, 2) }}</td>
                      <td class="text-right text-muted">₹{{ number_format((float)$b->balance_after, 2) }}</td>
                      <td class="text-muted" style="font-size:.82rem;">{{ \Illuminate\Support\Str::limit($b->note ?? '—', 50) }}</td>
                    </tr>
                  @empty
                    <tr><td colspan="7" class="text-center text-muted py-3">No balance history found.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
            @if($balanceHistories->hasPages())
              <div class="card-footer clearfix">{{ $balanceHistories->links('pagination::bootstrap-4') }}</div>
            @endif
          </div>
        </div>

      </div>
    </div>
  </div>
</section>
@endsection
