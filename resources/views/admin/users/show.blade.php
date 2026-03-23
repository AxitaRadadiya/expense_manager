@extends('admin.layouts.app')
@section('title', 'User Details')

@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">User Details</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
          <li class="breadcrumb-item active">{{ $user->name }}</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-4">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">{{ $user->name }}</h4>
        <p class="text-muted">{{ $user->email }}</p>

        <ul class="list-unstyled mt-3">
          <li><strong>Role:</strong> {{ $user->role->name ?? '—' }}</li>
          <li><strong>Mobile:</strong> {{ $user->mobile ?? '—' }}</li>
          <li><strong>Project:</strong> {{ optional($user->project)->name ?? '—' }}</li>
          <li><strong>Opening Balance:</strong> ₹ {{ number_format((float) $user->amount ?? 0, 2) }}</li>
        </ul>

        <div class="mt-3">
          <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary btn-sm">Edit</a>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">Debited (Expenses) — {{ $expenses->total() }} total</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Date</th>
                <th>Project</th>
                <th class="text-right">Amount (₹)</th>
                <th>Description</th>
                <th>Status</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @forelse($expenses as $i => $exp)
                <tr>
                  <td>{{ $expenses->firstItem() + $i }}</td>
                  <td>{{ optional($exp->expense_date)->format('d M Y') ?? '-' }}</td>
                  <td>{{ optional($exp->project)->name ?? '-' }}</td>
                  <td class="text-right">₹ {{ number_format((float) $exp->amount, 2) }}</td>
                  <td>{{ \Illuminate\Support\Str::limit($exp->description ?? '-', 80) }}</td>
                  <td>{{ ucfirst($exp->status ?? '-') }}</td>
                  <td class="text-right">
                    <a href="{{ route('expense.show', $exp->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center text-muted">No expenses found for this user.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if($expenses->hasPages())
          <div class="mt-3">{{ $expenses->links('pagination::bootstrap-4') }}</div>
        @endif
      </div>
    </div>
  </div>
</div>

@endsection
