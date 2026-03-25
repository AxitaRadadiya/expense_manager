@extends('admin.layouts.app')
@section('title', 'Users')

@section('content')

<style>
  /* ── Page Hero ── */
  .page-hero {
    background: linear-gradient(135deg, #006666 0%, #008d8d 60%, #00a8a8 100%);
    padding: 1.6rem 2rem 4.2rem;
    position: relative; overflow: hidden;
  }
  .page-hero::before {
    content:''; position:absolute; inset:0; pointer-events:none;
    background-image: radial-gradient(rgba(255,255,255,.08) 1px, transparent 1px);
    background-size: 26px 26px;
  }
  .page-hero .orb {
    position:absolute; border-radius:50%; pointer-events:none;
    width:200px; height:200px;
    background:radial-gradient(circle,rgba(255,255,255,.12) 0%,transparent 65%);
    top:-60px; right:40px;
  }
  .page-hero h1 {
    font-family:'Playfair Display',serif;
    font-size:1.5rem; font-weight:800; color:#fff; margin:0 0 .25rem;
    position:relative; z-index:2;
  }
  .page-hero p { color:rgba(255,255,255,.6); font-size:.82rem; margin:0; position:relative; z-index:2; }

  /* ── Pull-up card ── */
  .pull-card { margin-top:-2.4rem; position:relative; z-index:10; padding:0 1.5rem; }
  .main-card {
    background:#fff; border-radius:16px;
    box-shadow:0 6px 32px rgba(0,141,141,.10);
    border:1px solid #d0eded; overflow:hidden;
  }
  .main-card-head {
    padding:1.1rem 1.4rem; border-bottom:1px solid #e8f4f4;
    display:flex; align-items:center; justify-content:space-between;
    flex-wrap:wrap; gap:.75rem;
  }
  .main-card-title { font-size:.95rem; font-weight:800; color:#0d2e2e; display:flex; align-items:center; gap:.5rem; }
  .main-card-title i { color:#008d8d; }
  .main-card-body { padding:1.25rem 1.4rem; }

  /* ── Buttons ── */
  .btn-create {
    background:linear-gradient(135deg,#006666,#00b5b5);
    color:#fff !important; border-radius:8px; padding:.45rem 1.1rem;
    font-size:.82rem; font-weight:700; text-decoration:none;
    display:inline-flex; align-items:center; gap:.4rem; border:none;
    transition:transform .18s,box-shadow .18s;
    box-shadow:0 3px 12px rgba(0,141,141,.25);
  }
  .btn-create:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(0,141,141,.35); color:#fff !important; }

  /* ── Table ── */
  table.dataTable thead th {
    background:#008d8d !important; color:#fff !important;
    font-size:.7rem !important; font-weight:700 !important;
    text-transform:uppercase; letter-spacing:.7px;
    border-bottom:2px solid #006666 !important;
    padding:.72rem 1rem !important; white-space:nowrap;
  }
  table.dataTable tbody tr { border-bottom:1px solid #e8f4f4 !important; transition:background .15s; }
  table.dataTable tbody tr:hover { background:#e0f7f7 !important; }
  table.dataTable tbody td { padding:.7rem 1rem !important; font-size:.83rem; color:#0d2e2e; vertical-align:middle !important; }

  /* Badges */
  .sb { display:inline-block; border-radius:20px; padding:.2rem .75rem; font-size:.72rem; font-weight:700; }
  .sb-active   { background:#e0f7f7; color:#006666; border:1px solid #a0d8d8; }
  .sb-inactive { background:#fff1f2; color:#be123c; border:1px solid #fecdd3; }
  .role-chip   { background:#e0f7f7; color:#006666; border:1px solid #a0d8d8; border-radius:6px; padding:.15rem .6rem; font-size:.74rem; font-weight:700; }
  .count-badge { background:#e0f7f7; color:#006666; border:1px solid #a0d8d8; border-radius:6px; padding:.1rem .6rem; font-size:.72rem; font-weight:800; }

  /* DataTables */
  .dataTables_wrapper .dataTables_filter input { border:1.5px solid #c8e6e6; border-radius:7px; padding:.35rem .75rem; font-size:.83rem; }
  .dataTables_wrapper .dataTables_filter input:focus { border-color:#008d8d; outline:none; box-shadow:0 0 0 3px rgba(0,141,141,.12); }
  .dataTables_wrapper .dataTables_info { font-size:.78rem; color:#5a8080; }
  .dataTables_wrapper .dataTables_paginate .paginate_button.current,
  .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover { background:#008d8d !important; color:#fff !important; border-radius:6px; border:none !important; }
  .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background:#e0f7f7 !important; color:#006666 !important; border:none !important; border-radius:6px; }

  /* Alert */
  .alert-success-custom {
    background:#e0f7f7; border-left:4px solid #008d8d; border-radius:8px;
    padding:.75rem 1rem; color:#006666; font-size:.84rem; font-weight:600;
    display:flex; align-items:center; gap:.5rem; margin-bottom:1rem;
  }
  .user-name-cell {
    display:flex;
    align-items:center;
    gap:.7rem;
    min-width:0;
  }
  .user-list-avatar {
    width:34px;
    height:34px;
    object-fit:cover;
    border-radius:50%;
    border:2px solid #d0eded;
    flex-shrink:0;
    background:#fff;
  }
</style>

{{-- HERO --}}
<div class="page-hero">
  <div class="orb"></div>
  <div class="container-fluid" style="position:relative;z-index:2;">
    <h1><i class="fas fa-users mr-2" style="color:rgba(255,255,255,.85);font-size:1.1rem;"></i>Users</h1>
    <p>Manage all system users, roles and access.</p>
  </div>
</div>

{{-- CARD --}}
<div class="pull-card">
  <div class="container-fluid" style="padding:0;">

    @if(session('success'))
    <div class="alert-success-custom mt-3">
      <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    <div class="main-card">
      <div class="main-card-head">
        <div class="main-card-title">
          <i class="fas fa-list"></i> All Users
          <span class="count-badge">{{ $users->total() }}</span>
        </div>
        <a href="{{ route('users.create') }}" class="btn-create">
          <i class="fas fa-plus"></i> New User
        </a>
      </div>
      <div class="main-card-body">
        <div class="table-responsive">
          <table id="userTable" class="table table-hover w-100">
            <thead>
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Project</th>
                <th>Amount</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>
<div style="height:2rem;"></div>

@endsection
