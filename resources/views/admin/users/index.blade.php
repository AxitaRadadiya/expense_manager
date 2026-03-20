@extends('admin.layouts.app')
@section('title', 'Users')

@section('content')

<style>
  /* ── Page Header ── */
  .page-hero {
    background: linear-gradient(135deg, #0B1120 0%, #111C30 60%, #162244 100%);
    padding: 1.6rem 2rem 4.2rem;
    position: relative; overflow: hidden;
  }
  .page-hero::before {
    content:''; position:absolute; inset:0; pointer-events:none;
    background-image: radial-gradient(rgba(201,150,12,.15) 1px, transparent 1px);
    background-size: 26px 26px;
  }
  .page-hero .orb {
    position:absolute; border-radius:50%; pointer-events:none;
    width:200px; height:200px;
    background:radial-gradient(circle,rgba(201,150,12,.2) 0%,transparent 65%);
    top:-60px; right:40px;
  }
  .page-hero h1 {
    font-family:'Playfair Display',serif;
    font-size:1.5rem; font-weight:800; color:#fff; margin:0 0 .25rem;
    position:relative; z-index:2;
  }
  .page-hero p { color:rgba(255,255,255,.4); font-size:.82rem; margin:0; position:relative; z-index:2; }

  /* ── Pull-up card ── */
  .pull-card {
    margin-top: -2.4rem;
    position: relative; z-index: 10;
    padding: 0 1.5rem;
  }
  .main-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 6px 32px rgba(0,0,0,.1);
    border: 1px solid rgba(0,0,0,.04);
    overflow: hidden;
  }
  .main-card-head {
    padding: 1.1rem 1.4rem;
    border-bottom: 1px solid #f0f2f7;
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: .75rem;
  }
  .main-card-title {
    font-size: .95rem; font-weight: 800; color: #0D1A30;
    display: flex; align-items: center; gap: .5rem;
  }
  .main-card-title i { color: #C9960C; }

  /* ── Create button ── */
  .btn-create {
    background: linear-gradient(135deg, #9A6E00, #F5BE2E);
    color: #111 !important;
    border-radius: 8px;
    padding: .45rem 1.1rem;
    font-size: .82rem; font-weight: 700;
    text-decoration: none;
    display: inline-flex; align-items: center; gap: .4rem;
    border: none;
    transition: transform .18s, box-shadow .18s;
    box-shadow: 0 3px 12px rgba(201,150,12,.3);
  }
  .btn-create:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(201,150,12,.4); color:#111 !important; }

  /* ── Table ── */
  .main-card-body { padding: 1.25rem 1.4rem; }

  table.dataTable thead th {
    background: #0D1A30 !important;
    color: #fff !important;
    font-size: .7rem !important; font-weight: 700 !important;
    text-transform: uppercase; letter-spacing: .7px;
    border-bottom: 2px solid #C9960C !important;
    border-color: #1a2d4a !important;
    padding: .72rem 1rem !important;
    white-space: nowrap;
  }
  table.dataTable tbody tr { border-bottom: 1px solid #f0f2f7 !important; transition: background .15s; }
  table.dataTable tbody tr:hover { background: #fdfaf0 !important; }
  table.dataTable tbody td { padding: .7rem 1rem !important; font-size: .83rem; color: #2d3748; vertical-align: middle !important; }

  /* Status badges */
  .sb { display:inline-block; border-radius:20px; padding:.2rem .75rem; font-size:.72rem; font-weight:700; text-transform:capitalize; }
  .sb-completed  { background:#f0fdf4; color:#15803d; }
  .sb-cancelled  { background:#fff1f2; color:#be123c; }

  /* Role chip */
  .role-chip {
    background:#fdf8e8; color:#9A6E00;
    border:1px solid rgba(201,150,12,.25);
    border-radius:6px; padding:.15rem .6rem;
    font-size:.74rem; font-weight:700;
  }

  /* Action dropdown */
  .dropdown-menu {
    background:#111C30 !important;
    border:1px solid #1e3054 !important;
    border-radius:10px !important;
    box-shadow:0 10px 35px rgba(0,0,0,.4) !important;
    padding:.3rem !important;
    min-width:140px;
  }
  .dropdown-item {
    border-radius:6px; padding:.42rem .85rem;
    font-size:.82rem; color:#b0bed4 !important;
    transition:background .15s;
  }
  .dropdown-item:hover { background:rgba(201,150,12,.15) !important; color:#F5BE2E !important; }
  .dropdown-divider { border-color:#1e3054 !important; margin:.25rem 0 !important; }

  /* DataTables override */
  .dataTables_wrapper .dataTables_filter input {
    border:1.5px solid #dde2ec; border-radius:7px;
    padding:.35rem .75rem; font-size:.83rem;
    transition:border-color .2s;
  }
  .dataTables_wrapper .dataTables_filter input:focus {
    border-color:#C9960C; outline:none;
    box-shadow:0 0 0 3px rgba(201,150,12,.12);
  }
  .dataTables_wrapper .dataTables_info { font-size:.78rem; color:#8a98b4; }
  .dataTables_wrapper .dataTables_paginate .paginate_button.current,
  .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background:#C9960C !important; color:#111 !important;
    border-radius:6px; border:none !important;
  }
  .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background:#fdf8e8 !important; color:#9A6E00 !important;
    border:none !important; border-radius:6px;
  }

  /* Success alert */
  .alert-success-custom {
    background:#f0fdf4; border-left:4px solid #16a34a;
    border-radius:8px; padding:.75rem 1rem;
    color:#15803d; font-size:.84rem; font-weight:600;
    display:flex; align-items:center; gap:.5rem;
    margin-bottom:1rem;
  }
</style>

{{-- PAGE HERO --}}
<div class="page-hero">
  <div class="orb"></div>
  <div class="container-fluid" style="position:relative;z-index:2;">
    <h1><i class="fas fa-users mr-2" style="color:#C9960C;font-size:1.1rem;"></i>Users</h1>
    <p>Manage all system users, roles and access.</p>
  </div>
</div>

{{-- PULL-UP CARD --}}
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
          <span style="background:#fdf8e8;color:#9A6E00;border:1px solid rgba(201,150,12,.25);border-radius:6px;padding:.1rem .6rem;font-size:.72rem;font-weight:800;">
            {{ $users->total() }}
          </span>
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

@endsection