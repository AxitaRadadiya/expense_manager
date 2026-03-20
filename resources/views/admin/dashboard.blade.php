@extends('admin.layouts.app')
@section('title', 'Dashboard')

@section('content')

<style>
  .content-wrapper { background: #F0F2F7 !important; }

  /* ══ HERO ══ */
  .dash-hero {
    background: linear-gradient(135deg, #0B1120 0%, #0F1B33 50%, #162244 100%);
    padding: 2.2rem 2rem 5rem;
    position: relative;
    overflow: hidden;
  }
  .dash-hero::before {
    content: '';
    position: absolute; inset: 0; pointer-events: none;
    background-image: radial-gradient(rgba(201,150,12,.18) 1px, transparent 1px);
    background-size: 28px 28px;
  }
  .dash-hero .orb { position: absolute; border-radius: 50%; pointer-events: none; }
  .orb-a { width:260px; height:260px; background: radial-gradient(circle, rgba(201,150,12,.22) 0%, transparent 65%); top:-80px; right:60px; }
  .orb-b { width:180px; height:180px; background: radial-gradient(circle, rgba(245,190,46,.12) 0%, transparent 70%); bottom:0; right:320px; }
  .orb-c { width:100px; height:100px; background: radial-gradient(circle, rgba(201,150,12,.1) 0%, transparent 70%); top:30px; left:38%; }
  .dash-hero h1 {
    font-family: 'Playfair Display', serif;
    font-size: 1.65rem; font-weight: 800;
    color: #fff; margin: 0 0 .3rem;
  }
  .dash-hero p { color: rgba(255,255,255,.45); font-size: .87rem; margin: 0; }
  .dash-date-pill {
    background: rgba(201,150,12,.15);
    border: 1px solid rgba(201,150,12,.3);
    color: #F5BE2E;
    border-radius: 20px; padding: .4rem 1.1rem;
    font-size: .78rem; font-weight: 700;
    display: inline-flex; align-items: center; gap: .45rem;
  }

  /* ══ STAT CARDS ══ */
  .dash-stats { margin-top:-3rem; position:relative; z-index:10; padding:0 1.5rem; }
  .scard {
    background: #fff; border-radius: 16px;
    padding: 1.4rem 1.5rem;
    box-shadow: 0 6px 30px rgba(0,0,0,.1);
    display: flex; align-items: center; gap: 1.1rem;
    height: 100%; position: relative; overflow: hidden;
    transition: transform .22s, box-shadow .22s;
    border: 1px solid rgba(0,0,0,.04);
  }
  .scard:hover { transform: translateY(-5px); box-shadow: 0 14px 40px rgba(0,0,0,.14); }
  .scard-stripe { position:absolute; top:0; left:0; width:4px; height:100%; border-radius:16px 0 0 16px; }
  .scard-watermark { position:absolute; bottom:-8px; right:-4px; font-size:4.5rem; opacity:.04; line-height:1; }
  .scard-icon { width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:1.2rem; }
  .scard-num   { font-size:2.1rem; font-weight:800; line-height:1; color:#0D1A30; margin-bottom:.1rem; }
  .scard-label { font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#aab; margin-bottom:.2rem; }
  .scard-sub   { font-size:.74rem; font-weight:600; }

  /* ══ SECTION HEADING ══ */
  .sh {
    font-size:.68rem; font-weight:800; text-transform:uppercase; letter-spacing:1.8px;
    color:#8a98b4; margin:0 0 1rem;
    display:flex; align-items:center; gap:.6rem;
  }
  .sh .sh-icon {
    width:22px; height:22px;
    background:linear-gradient(135deg,#9A6E00,#C9960C);
    border-radius:6px;
    display:inline-flex; align-items:center; justify-content:center;
    font-size:.62rem; color:#fff;
  }
  .sh::after { content:''; flex:1; height:1px; background:#e8eaf0; }

  /* ══ PANEL ══ */
  .panel { background:#fff; border-radius:16px; box-shadow:0 4px 24px rgba(0,0,0,.08); overflow:hidden; height:100%; border:1px solid rgba(0,0,0,.04); }
  .panel-head { padding:1.1rem 1.5rem; border-bottom:1px solid #f0f2f7; display:flex; align-items:center; justify-content:space-between; }
  .panel-title { font-size:.92rem; font-weight:800; color:#0D1A30; display:flex; align-items:center; gap:.5rem; }
  .panel-title i { color:#C9960C; }
  .panel-body { padding:1.3rem 1.5rem; }
  .year-badge { background:#fdf8e8; border:1px solid rgba(201,150,12,.25); color:#9A6E00; border-radius:6px; padding:.2rem .75rem; font-size:.75rem; font-weight:800; }

</style>

{{-- HERO --}}
<div class="dash-hero">
  <div class="orb orb-a"></div>
  <div class="orb orb-b"></div>
  <div class="orb orb-c"></div>
  <div class="container-fluid" style="position:relative;z-index:2;">
    <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap:1rem;">
      <div>
        <h1>Good day, Admin ✦</h1>
        <p>Here's a live overview of your print operations.</p>
      </div>
      <div class="dash-date-pill">
        <i class="far fa-calendar-alt"></i> {{ date('l, d M Y') }}
      </div>
    </div>
  </div>
</div>

{{-- STAT CARDS --}}
<div class="dash-stats">
  <div class="row">

    <div class="col-xl-3 col-md-6 col-sm-6 col-12 mb-3">
      <div class="scard">
        <div class="scard-stripe" style="background:linear-gradient(180deg,#9A6E00,#F5BE2E);"></div>
        <div class="scard-icon" style="background:#fdf8e8;"><i class="fas fa-layer-group" style="color:#C9960C;"></i></div>
        <div style="flex:1;">
          <div class="scard-label">Total Job Cards</div>
          <div class="scard-num">{{ $totalJobCards }}</div>
          <div class="scard-sub" style="color:#C9960C;"><i class="fas fa-history mr-1"></i>All time</div>
        </div>
        <div class="scard-watermark"><i class="fas fa-layer-group"></i></div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6 col-sm-6 col-12 mb-3">
      <div class="scard">
        <div class="scard-stripe" style="background:linear-gradient(180deg,#1d4ed8,#60a5fa);"></div>
        <div class="scard-icon" style="background:#eff6ff;"><i class="fas fa-print" style="color:#2563eb;"></i></div>
        <div style="flex:1;">
          <div class="scard-label">Total Jobs</div>
          <div class="scard-num">{{ $totalJobs }}</div>
          <div class="scard-sub" style="color:#2563eb;"><i class="fas fa-briefcase mr-1"></i>All products</div>
        </div>
        <div class="scard-watermark"><i class="fas fa-print"></i></div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6 col-sm-6 col-12 mb-3">
      <div class="scard">
        <div class="scard-stripe" style="background:linear-gradient(180deg,#15803d,#4ade80);"></div>
        <div class="scard-icon" style="background:#f0fdf4;"><i class="fas fa-address-book" style="color:#16a34a;"></i></div>
        <div style="flex:1;">
          <div class="scard-label">Total Contacts</div>
          <div class="scard-num">{{ $totalContacts }}</div>
          <div class="scard-sub" style="color:#16a34a;"><i class="fas fa-users mr-1"></i>Registered parties</div>
        </div>
        <div class="scard-watermark"><i class="fas fa-address-book"></i></div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6 col-sm-6 col-12 mb-3">
      <div class="scard">
        <div class="scard-stripe" style="background:linear-gradient(180deg,#7e22ce,#a855f7);"></div>
        <div class="scard-icon" style="background:#faf5ff;"><i class="fas fa-calendar-day" style="color:#9333ea;"></i></div>
        <div style="flex:1;">
          <div class="scard-label">Today's Cards</div>
          <div class="scard-num">{{ $todayJobCards }}</div>
          <div class="scard-sub" style="color:#9333ea;"><i class="far fa-clock mr-1"></i>{{ date('d M Y') }}</div>
        </div>
        <div class="scard-watermark"><i class="fas fa-calendar-day"></i></div>
      </div>
    </div>

  </div>
</div>
@endsection