<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Expense Manager</title>
  <link rel="icon" type="image/x-icon" href="#">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
  @include('admin.particle.css')

  <style>
    /* ═══════════════════════════════════════════════
       THEME : Deep Navy + Gold Amber  |  PrintPro Admin
       Primary   #C9960C   (gold)
       Accent    #F5BE2E   (bright gold)
       Dark      #C9960C   deep
       Sidebar   #0B1120 → #111C30
       Navbar    #0D1526 → #13203A
    ═══════════════════════════════════════════════ */
    :root {
      --pri       : #C9960C;
      --pri-lt    : #F5BE2E;
      --pri-dk    : #9A6E00;
      --sb-from   : #0B1120;
      --sb-to     : #111C30;
      --nb-from   : #0D1526;
      --nb-to     : #13203A;
      --bg        : #F0F2F7;
      --sb-w      : 225px;
      --sb-mini   : 4.6rem;
      --nb-h      : 57px;
      --radius    : 10px;
      --shadow    : 0 4px 24px rgba(0,0,0,.13);
    }

    *, *::before, *::after { box-sizing: border-box; }

    body {
      font-family: "DM Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      background: var(--bg);
      color: #1e1e1e;
    }

    /* Scrollbar */
    ::-webkit-scrollbar            { width:5px; height:5px; }
    ::-webkit-scrollbar-track      { background:#0B1120; }
    ::-webkit-scrollbar-thumb      { background:#2a3a58; border-radius:10px; }
    ::-webkit-scrollbar-thumb:hover{ background:var(--pri); }

    /* ════════════════ NAVBAR ════════════════════ */
    .main-header.navbar {
      background: linear-gradient(135deg, var(--nb-from) 0%, var(--nb-to) 100%) !important;
      border-bottom: 2px solid rgba(201,150,12,.5) !important;
      box-shadow: 0 3px 20px rgba(0,0,0,.45);
      min-height: var(--nb-h);
      padding: 0 1rem;
      z-index: 1040;
    }

    .main-header .nav-link,
    .navbar-light .navbar-nav .nav-link,
    .navbar-dark  .navbar-nav .nav-link {
      color: rgba(255,255,255,.75) !important;
      border-radius: 7px;
      margin: 0 2px;
      transition: background .2s, color .2s;
    }
    .main-header .nav-link:hover {
      background: rgba(201,150,12,.2) !important;
      color: var(--pri-lt) !important;
    }

    /* Navbar dropdown */
    .main-header .dropdown-menu {
      background: #111C30;
      border: 1px solid #1e3054;
      border-radius: var(--radius);
      box-shadow: 0 10px 35px rgba(0,0,0,.4);
      padding: .4rem;
      min-width: 200px;
    }
    .main-header .dropdown-item {
      border-radius: 6px;
      padding: .45rem .9rem;
      font-size: .85rem;
      color: #b0bed4;
      transition: background .15s;
    }
    .main-header .dropdown-item:hover {
      background: rgba(201,150,12,.18);
      color: var(--pri-lt);
    }
    .main-header .dropdown-divider   { border-color: #1e3054; margin: .25rem 0; }
    .main-header .dropdown-header    { color:#4a6080 !important; font-size:.7rem; letter-spacing:1px; text-transform:uppercase; }

    /* User pill */
    .navbar-user-pill {
      background: rgba(201,150,12,.15);
      border: 1px solid rgba(201,150,12,.25);
      border-radius: 8px;
      padding: .3rem .75rem !important;
      display: flex; align-items: center; gap:.4rem;
    }
    .navbar-user-pill .user-avatar {
      background: rgba(201,150,12,.3);
      border-radius: 50%;
      width:28px; height:28px;
      display:inline-flex; align-items:center; justify-content:center;
    }

    /* ════════════════ SIDEBAR ═══════════════════ */
    .main-sidebar {
      background: linear-gradient(180deg, var(--sb-from) 0%, var(--sb-to) 100%) !important;
      width: var(--sb-w) !important;
      box-shadow: 4px 0 24px rgba(0,0,0,.5);
      border-right: 1px solid #1a2d4a;
      overflow-x: hidden;
      overflow-y: auto;
      transition: width .3s ease, margin-left .3s ease !important;
    }

    /* Brand */
    [class*=sidebar-dark] .brand-link,
    .brand-link {
      background: rgba(0,0,0,.3) !important;
      border-bottom: 1px solid rgba(201,150,12,.2) !important;
      color: #fff !important;
      padding: .9rem 1rem;
      display: flex;
      align-items: center;
      gap: .65rem;
      min-height: var(--nb-h);
      width: 100%;
      white-space: nowrap;
      overflow: hidden;
    }
    .brand-link .brand-image {
      width: 32px; height: 32px;
      border-radius: 8px;
      border: 2px solid var(--pri);
      flex-shrink: 0;
      object-fit: cover;
    }
    .brand-link .brand-text {
      font-family: 'Playfair Display', serif;
      font-size: .92rem;
      font-weight: 700;
      color: #fff;
      white-space: nowrap;
    }

    .sidebar {
      padding: .5rem .35rem;
      overflow-x: hidden;
    }

    /* ── Sidebar section label ── */
    .nav-sidebar .nav-header {
      font-size: .62rem;
      font-weight: 700;
      letter-spacing: 1.6px;
      text-transform: uppercase;
      color: #3a5070;
      padding: .9rem .85rem .3rem;
    }

    .nav-sidebar .nav-item {
      width: 100%;
      margin-bottom: 2px;
    }
    .nav-sidebar > .nav-item > .nav-link,
    .nav-sidebar .nav-link {
      color: rgba(180,200,230,.65) !important;
      border-radius: 8px;
      padding: .52rem .75rem !important;
      display: flex;
      align-items: center;
      gap: .55rem;
      font-size: .85rem;
      font-weight: 500;
      transition: background .2s, color .2s;
      white-space: nowrap;
      overflow: hidden;
    }
    .nav-sidebar .nav-link:hover {
      background: rgba(201,150,12,.15) !important;
      color: var(--pri-lt) !important;
    }
    .nav-sidebar > .nav-item > .nav-link.active {
      background: linear-gradient(135deg, var(--pri-dk), var(--pri)) !important;
      color: #fff !important;
      box-shadow: 0 4px 16px rgba(201,150,12,.35);
    }

    .nav-sidebar .nav-icon {
      font-size: 14px !important;
      width: 20px;
      min-width: 20px;
      text-align: center;
      color: var(--pri);
      flex-shrink: 0;
    }
    .nav-sidebar > .nav-item > .nav-link.active .nav-icon { color: #fff; }

    .nav-sidebar p {
      margin: 0; flex: 1;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      transition: opacity .25s;
    }

    /* Treeview */
    .nav-treeview {
      padding-left: .6rem;
      background: rgba(0,0,0,.15);
      border-radius: 0 0 8px 8px;
    }
    .nav-treeview .nav-link {
      font-size: .8rem !important;
      color: rgba(180,200,230,.45) !important;
      padding: .38rem .65rem !important;
      border-radius: 6px;
    }
    .nav-treeview .nav-link:hover {
      background: rgba(201,150,12,.12) !important;
      color: var(--pri-lt) !important;
    }
    .nav-treeview .nav-link.active {
      background: rgba(201,150,12,.18) !important;
      color: var(--pri-lt) !important;
    }

    /* ════════════════ CONTENT WRAPPER ══════════ */
    .content-wrapper {
      background: var(--bg) !important;
      min-height: calc(100vh - var(--nb-h));
    }

    /* ════════════════ CARDS ════════════════════ */
    .card {
      border: none !important;
      border-radius: var(--radius) !important;
      box-shadow: var(--shadow) !important;
    }
    .card-header {
      background: #fff !important;
      border-bottom: 2px solid #f0f2f7 !important;
      border-radius: var(--radius) var(--radius) 0 0 !important;
      font-weight: 700; font-size: .9rem; color: #1a2d4a;
      padding: .9rem 1.25rem;
    }

    /* ════════════════ BUTTONS ══════════════════ */
    .btn {
      border-radius: 7px !important;
      font-weight: 600; font-size: .82rem;
      padding: .42rem .95rem;
      border: none;
      transition: transform .18s, box-shadow .18s;
    }
    .btn:hover  { transform: translateY(-2px); box-shadow: 0 5px 16px rgba(0,0,0,.2); }
    .btn:active { transform: translateY(0); }

    .btn-primary { background: linear-gradient(135deg, var(--pri-dk), var(--pri-lt)) !important; color:#111 !important; }
    .btn-success { background: linear-gradient(135deg,#1e8449,#27ae60) !important;  color:#fff !important; }
    .btn-danger  { background: linear-gradient(135deg,#922b21,#e74c3c) !important;  color:#fff !important; }
    .btn-warning { background: linear-gradient(135deg,#b7770d,#f39c12) !important;  color:#fff !important; }
    .btn-info    { background: linear-gradient(135deg,#1a5276,#2980b9) !important;  color:#fff !important; }
    .btn-default { background:#efefef !important; color:#333 !important; border:1px solid #ddd !important; }
    .btn-default:hover { background:#e2e2e2 !important; }
    .btn-xs { padding:.2rem .5rem;  font-size:.72rem; border-radius:5px !important; }
    .btn-sm { padding:.3rem .72rem; font-size:.78rem; }

    /* ════════════════ TABLES ═══════════════════ */
    .table thead th {
      background: #0D1A30;
      color: #fff;
      font-weight: 700; font-size: .78rem;
      text-transform: uppercase; letter-spacing: .5px;
      border-bottom: 2px solid var(--pri) !important;
      border-color: #1a2d4a !important;
      padding: .68rem .9rem;
    }
    .table td {
      vertical-align: middle; font-size: .85rem;
      color: #333; padding: .6rem .9rem;
      border-color: #ebebeb;
    }
    .table-hover tbody tr:hover   { background: #fdf8e8; }
    .table-striped tbody tr:nth-of-type(odd) { background: #fafafa; }

    /* Pagination */
    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
      background: var(--pri) !important; color:#111 !important;
      border-radius:6px; border:none !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
      background: #fdf8e8 !important; color:var(--pri-dk) !important;
      border:none !important; border-radius:6px;
    }

    /* ════════════════ FORMS ════════════════════ */
    .form-control {
      border-radius: 7px !important;
      border: 1.5px solid #dde2ec;
      font-size: .85rem; color: #1e1e1e;
      background: #fff;
      transition: border-color .2s, box-shadow .2s;
    }
    .form-control:focus {
      border-color: var(--pri) !important;
      box-shadow: 0 0 0 3px rgba(201,150,12,.15) !important;
    }
    label { font-size:.8rem; font-weight:600; color:#444; margin-bottom:.3rem; }

    /* Select2 */
    .select2-container--default .select2-selection--single {
      border: 1.5px solid #dde2ec !important;
      border-radius: 7px !important; height:36px !important;
      display:flex; align-items:center;
    }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open  .select2-selection--single {
      border-color:var(--pri) !important;
      box-shadow:0 0 0 3px rgba(201,150,12,.15) !important;
    }
    .select2-results__option--highlighted { background:var(--pri) !important; color:#111 !important; }

    /* ════════════════ BADGES ═══════════════════ */
    .badge { border-radius:5px; font-size:.72rem; font-weight:600; padding:.3em .65em; }
    .badge-primary   { background:var(--pri);  color:#111; }
    .badge-success   { background:#27ae60;     color:#fff; }
    .badge-warning   { background:#f39c12;     color:#fff; }
    .badge-danger    { background:#e74c3c;     color:#fff; }
    .badge-info      { background:#2980b9;     color:#fff; }
    .badge-secondary { background:#555;        color:#fff; }

    /* ════════════════ ALERTS ═══════════════════ */
    .alert { border:none; border-radius:var(--radius); font-size:.85rem; border-left:4px solid; }
    .alert-success { background:#eafaf1; border-color:#27ae60; color:#1e8449; }
    .alert-danger  { background:#fdf3f3; border-color:#e74c3c; color:#922b21; }
    .alert-warning { background:#fdf8e8; border-color:#C9960C; color:#7d5c00; }
    .alert-info    { background:#eaf4fb; border-color:#2980b9; color:#1a5276; }

    /* ════════════════ FOOTER ═══════════════════ */
    .main-footer {
      background: #0D1526 !important;
      border-top: 2px solid rgba(201,150,12,.3) !important;
      color: #4a6080; font-size: .8rem;
      padding: .75rem 1.25rem;
    }
    .main-footer a { color: var(--pri-lt); font-weight: 600; }
    .main-footer a:hover { color: #fff; }

    /* ════════════════ LAYOUT MARGINS ═══════════ */
    @media (min-width: 768px) {
      body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .main-header,
      body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .content-wrapper,
      body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .main-footer {
        margin-left: var(--sb-w);
        transition: margin-left .3s ease;
      }
    }
    @media (max-width: 991.98px) {
      body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .main-header,
      body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .content-wrapper,
      body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .main-footer {
        margin-left: 0;
      }
    }

    /* ════════════════ MISC ═════════════════════ */
    .required { color:#e53935; margin-left:3px; font-weight:700; }
    .elevation-2 { box-shadow:0 3px 10px rgba(0,0,0,.1) !important; }
    .elevation-4 { box-shadow:0 4px 18px rgba(0,0,0,.22) !important; }
    .small-box { border-radius: var(--radius) !important; overflow:hidden; }
    .small-box:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(0,0,0,.18) !important; transition: .25s; }
    .info-box  { border-radius: var(--radius) !important; box-shadow: var(--shadow) !important; }

    .suggestions-list {
      list-style:none; padding:0; margin:0;
      position:absolute; width:100%; max-height:200px; overflow-y:auto;
      background:#fff; border-radius:8px;
      box-shadow:0 8px 28px rgba(0,0,0,.14);
      z-index:1000;
    }
    .suggestions-list li { padding:10px 14px; cursor:pointer; font-size:.85rem; }
    .suggestions-list li:hover { background:#fdf8e8; color:var(--pri-dk); }

    @media (max-width:767.98px) {
      .form-group label { display:block; width:100%; }
      .row.input-row .col { flex:0 0 100%; max-width:100%; margin-bottom:10px; }
    }
    @media (min-width:768px) and (max-width:1199.98px) {
      .row.input-row .col { flex:0 0 50%; max-width:50%; margin-bottom:10px; }
    }
    @media (min-width:1200px) {
      .row.input-row .col { flex:0 0 14.285%; max-width:14.285%; }
    }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed text-sm">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-dark">
    @include('admin.particle.navbar')
  </nav>
  <!-- Main Sidebar -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    @include('admin.particle.sidebar')
  </aside>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    @yield('content')
  </div>

  <footer class="main-footer">
    @include('admin.particle.footer')
  </footer>

  <aside class="control-sidebar control-sidebar-dark"></aside>
</div>

@include('admin.particle.script')
@yield('style')
@yield('pageScript')
</body>
</html>