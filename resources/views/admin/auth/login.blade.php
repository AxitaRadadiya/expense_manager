<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'App') }} — Sign In</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ═══════════════════════════════════════════
           THEME  —  Light Teal
           Primary  #008d8d  |  Light #00b5b5
           Dark     #006666  |  Tint  #e0f7f7
        ═══════════════════════════════════════════ */
        :root {
            --pri     : #008d8d;
            --pri-lt  : #00b5b5;
            --pri-dk  : #006666;
            --pri-tint: #e0f7f7;
            --pri-mute: #b2dfdf;
            --bg      : #f0f6f6;
            --card    : #ffffff;
            --bdr     : #c8e6e6;
            --text-dk : #0d2e2e;
            --text-md : #2a5050;
            --text-sf : #5a8080;
            --text-lt : #a0cece;
            --radius  : 10px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; font-family: "DM Sans", -apple-system, sans-serif; }

        body {
            background: var(--bg);
            min-height: 100vh;
            display: flex;
            align-items: stretch;
        }

        ::-webkit-scrollbar            { width: 5px; }
        ::-webkit-scrollbar-track      { background: #e0f0f0; }
        ::-webkit-scrollbar-thumb      { background: #a0cece; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover{ background: var(--pri); }

        /* ══ SHELL ══ */
        .ls { display: flex; width: 100%; min-height: 100vh; }

        /* ══════════════════════════════
           LEFT PANEL
        ══════════════════════════════ */
        .ls-l {
            flex: 1;
            display: none;
            flex-direction: column;
            justify-content: space-between;
            padding: 52px 52px;
            background: linear-gradient(160deg, #006666 0%, #008d8d 50%, #00a8a8 100%);
            position: relative;
            overflow: hidden;
        }
        @media (min-width: 1024px) { .ls-l { display: flex; } }

        /* Dot grid */
        .ls-grid {
            position: absolute; inset: 0; pointer-events: none;
            background-image: radial-gradient(rgba(255,255,255,.12) 1px, transparent 1px);
            background-size: 32px 32px;
        }

        /* Blobs */
        .ls-blob { position: absolute; border-radius: 50%; pointer-events: none; }
        .ls-blob-a {
            width: 420px; height: 420px;
            background: radial-gradient(circle, rgba(255,255,255,.15) 0%, transparent 65%);
            top: -140px; right: -100px;
        }
        .ls-blob-b {
            width: 260px; height: 260px;
            background: radial-gradient(circle, rgba(255,255,255,.08) 0%, transparent 70%);
            bottom: 30px; left: -60px;
        }
        .ls-blob-c {
            width: 150px; height: 150px;
            background: radial-gradient(circle, rgba(0,181,181,.25) 0%, transparent 70%);
            top: 40%; left: 30%;
        }

        /* Brand */
        .ls-brand {
            position: relative; z-index: 2;
            display: flex; align-items: center; gap: 12px;
        }
        .ls-brand-icon {
            width: 44px; height: 44px; border-radius: 12px;
            border: 2px solid rgba(255,255,255,.4);
            background: rgba(255,255,255,.15);
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
            backdrop-filter: blur(4px);
        }
        .ls-brand-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.15rem; font-weight: 800; color: #fff;
        }

        /* Hero */
        .ls-hero { position: relative; z-index: 2; }

        .ls-badge {
            display: inline-flex; align-items: center; gap: 7px;
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.3);
            border-radius: 20px; padding: 5px 14px; margin-bottom: 22px;
            backdrop-filter: blur(4px);
        }
        .ls-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: #fff;
            animation: blink 2s ease-in-out infinite;
        }
        @keyframes blink { 0%,100%{opacity:1;} 50%{opacity:.3;} }
        .ls-badge span {
            font-size: .68rem; font-weight: 700;
            letter-spacing: .12em; text-transform: uppercase; color: #fff;
        }

        .ls-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.7rem; font-weight: 700; line-height: 1.15;
            color: rgba(255,255,255,.95); letter-spacing: -.02em; margin-bottom: 16px;
        }
        .ls-title em {
            font-style: normal;
            color: #fff;
            text-decoration: underline;
            text-decoration-color: rgba(255,255,255,.4);
            text-underline-offset: 6px;
        }

        .ls-desc {
            font-size: .88rem; font-weight: 300;
            color: rgba(255,255,255,.6); line-height: 1.9; max-width: 300px;
        }

        /* Feature pills */
        .ls-features {
            position: relative; z-index: 2;
            display: flex; flex-direction: column; gap: .75rem;
        }
        .ls-feat {
            display: flex; align-items: center; gap: .75rem;
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.2);
            border-radius: 10px; padding: .75rem 1rem;
            backdrop-filter: blur(4px);
            transition: background .2s;
        }
        .ls-feat:hover { background: rgba(255,255,255,.16); }
        .ls-feat-icon {
            width: 36px; height: 36px; border-radius: 9px;
            background: rgba(255,255,255,.2);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .ls-feat-icon svg { width: 16px; height: 16px; fill: #fff; }
        .ls-feat-text { }
        .ls-feat-title { font-size: .82rem; font-weight: 700; color: #fff; margin-bottom: .1rem; }
        .ls-feat-sub   { font-size: .72rem; color: rgba(255,255,255,.55); }

        /* Stats strip */
        .ls-stats {
            position: relative; z-index: 2;
            display: flex; align-items: center;
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.2);
            border-radius: var(--radius); padding: 20px 24px;
            backdrop-filter: blur(4px);
        }
        .ls-stat { flex: 1; text-align: center; }
        .ls-stat-num {
            font-family: 'Playfair Display', serif;
            font-size: 1.85rem; font-weight: 700; color: #fff; line-height: 1;
        }
        .ls-stat-num span { color: rgba(255,255,255,.6); font-size: 1.2rem; }
        .ls-stat-lbl {
            font-size: .68rem; font-weight: 600;
            letter-spacing: .08em; text-transform: uppercase;
            color: rgba(255,255,255,.5); margin-top: 5px;
        }
        .ls-stat-div { width: 1px; height: 36px; background: rgba(255,255,255,.2); flex-shrink: 0; }

        /* ══════════════════════════════
           RIGHT PANEL
        ══════════════════════════════ */
        .ls-r {
            width: 100%;
            max-width: 480px;
            background: var(--card);
            border-left: 1px solid var(--bdr);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 56px 48px;
            position: relative;
        }
        @media (max-width: 600px) { .ls-r { padding: 40px 24px; max-width: 100%; } }

        /* Teal top accent bar */
        .ls-r::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--pri-dk), var(--pri-lt));
        }

        /* Corner decoration */
        .ls-r::after {
            content: '';
            position: absolute; bottom: 0; right: 0;
            width: 120px; height: 120px;
            background: radial-gradient(circle at bottom right, rgba(0,141,141,.06) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Mobile logo */
        .ls-mob-logo {
            display: none; justify-content: center; margin-bottom: 36px;
            align-items: center; gap: 10px;
        }
        @media (max-width: 1023px) { .ls-mob-logo { display: flex; } }
        .ls-mob-logo .mob-icon {
            width: 38px; height: 38px; border-radius: 10px;
            background: linear-gradient(135deg, var(--pri-dk), var(--pri-lt));
            display: flex; align-items: center; justify-content: center;
        }
        .ls-mob-logo .mob-icon svg { width: 18px; height: 18px; fill: #fff; }
        .ls-mob-logo span {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem; font-weight: 800; color: var(--text-dk);
        }

        /* Session status */
        .ls-status {
            background: #e0f7f7; border-left: 3px solid var(--pri);
            border-radius: 8px; padding: .75rem 1rem;
            font-size: .83rem; color: var(--pri-dk);
            margin-bottom: 1.2rem;
            display: flex; align-items: center; gap: .5rem;
        }

        /* Heading */
        .ls-rhead { margin-bottom: 2rem; }
        .ls-rtitle {
            font-family: 'Playfair Display', serif;
            font-size: 1.9rem; font-weight: 800;
            color: var(--text-dk); margin-bottom: .5rem;
        }
        .ls-rbar {
            display: block; width: 36px; height: 3px;
            background: linear-gradient(90deg, var(--pri-dk), var(--pri-lt));
            border-radius: 2px; margin-bottom: .65rem;
        }
        .ls-rsub { font-size: .87rem; color: var(--text-sf); font-weight: 400; }

        /* Fields */
        .ls-fld { margin-bottom: 1.2rem; }
        .ls-lbl {
            display: block; font-size: .78rem; font-weight: 700;
            color: var(--text-md); margin-bottom: .45rem; letter-spacing: .02em;
        }

        .ls-fw { position: relative; display: flex; align-items: center; }
        .ls-ico {
            position: absolute; left: 13px;
            display: flex; align-items: center; pointer-events: none;
        }
        .ls-ico svg { width: 15px; height: 15px; fill: var(--text-lt); }

        .ls-inp {
            width: 100%; height: 44px;
            border: 1.5px solid var(--bdr);
            border-radius: 8px;
            padding: 0 2.6rem 0 2.6rem;
            font-size: .88rem; font-family: "DM Sans", sans-serif;
            color: var(--text-dk); background: #fff;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .ls-inp:focus {
            border-color: var(--pri);
            box-shadow: 0 0 0 3px rgba(0,141,141,.12);
        }
        .ls-inp::placeholder { color: var(--text-lt); }

        .ls-eye {
            position: absolute; right: 12px;
            background: none; border: none; cursor: pointer;
            padding: 0; display: flex; align-items: center;
        }
        .ls-eye svg { width: 16px; height: 16px; fill: var(--text-lt); transition: fill .18s; }
        .ls-eye:hover svg { fill: var(--pri); }

        /* Error */
        .ls-err {
            font-size: .76rem; color: #dc2626; margin-top: .35rem;
            display: flex; align-items: center; gap: .3rem;
        }
        .ls-err::before { content: '⚠'; font-size: .7rem; }

        /* Remember */
        .ls-rem {
            display: flex; align-items: center; gap: .5rem;
            margin-bottom: 1.5rem;
        }
        .ls-rem input[type="checkbox"] { accent-color: var(--pri); width:15px; height:15px; cursor:pointer; }
        .ls-rem label { font-size: .82rem; color: var(--text-sf); cursor: pointer; margin: 0; }

        /* Submit */
        .ls-btn {
            width: 100%; height: 46px;
            background: linear-gradient(135deg, var(--pri-dk), var(--pri-lt));
            border: none; border-radius: 8px;
            color: #fff; font-family: "DM Sans", sans-serif;
            font-size: .9rem; font-weight: 700; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            box-shadow: 0 4px 18px rgba(0,141,141,.28);
            transition: transform .18s, box-shadow .18s;
            margin-bottom: 16px;
            letter-spacing: .02em;
        }
        .ls-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(0,141,141,.38);
        }
        .ls-btn:active { transform: none; }
        .ls-btn .arrow { transition: transform .2s; }
        .ls-btn:hover .arrow { transform: translateX(4px); }

        /* Forgot */
        .ls-foot { text-align: center; }
        .ls-sep {
            display: flex; align-items: center; gap: 12px;
            margin: 4px 0 16px;
        }
        .ls-sep::before, .ls-sep::after {
            content:''; flex:1; height:1px; background: var(--bdr);
        }
        .ls-sep span { font-size: .7rem; color: var(--text-lt); }
        .ls-forgot {
            font-size: .82rem; color: var(--text-sf);
            text-decoration: none; font-weight: 600; transition: color .2s;
        }
        .ls-forgot:hover { color: var(--pri); }

        /* ── Animated teal line under form card ── */
        .ls-prog {
            position: absolute; bottom: 0; left: 0;
            height: 2px; width: 100%;
            background: linear-gradient(90deg, transparent, var(--pri-lt), transparent);
            background-size: 200% 100%;
            animation: shimmer 3s linear infinite;
        }
        @keyframes shimmer { 0%{background-position:200% 0;} 100%{background-position:-200% 0;} }
    </style>
</head>
<body>

<div class="ls">

    {{-- ══ LEFT PANEL ══ --}}
    <div class="ls-l">
        <div class="ls-grid"></div>
        <div class="ls-blob ls-blob-a"></div>
        <div class="ls-blob ls-blob-b"></div>
        <div class="ls-blob ls-blob-c"></div>

        {{-- Brand --}}
        <div class="ls-brand">
            <div class="ls-brand-icon">
                <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:#fff;">
                    <path d="M12 2a10 10 0 1 0 0 20A10 10 0 0 0 12 2zm-1 14H9V10h2v6zm0-8H9V6h2v2zm4 8h-2v-4h2v4zm0-6h-2v-2h2v2z"/>
                </svg>
            </div>
            <span class="ls-brand-name">{{ config('app.name', 'Expense Manager') }}</span>
        </div>

        {{-- Hero --}}
        <div class="ls-hero">
            <div class="ls-badge">
                <div class="ls-dot"></div>
                <span>Expense Management System</span>
            </div>
            <h2 class="ls-title">
                Track every<br>
                expense with<br>
                <em>clarity & control.</em>
            </h2>
            <p class="ls-desc">
                Monitor transfers, manage project budgets, track
                team expenses and keep your finances running smoothly.
            </p>
        </div>

        {{-- Feature pills --}}
        <div class="ls-features">
            <div class="ls-feat">
                <div class="ls-feat-icon">
                    <svg viewBox="0 0 24 24"><path d="M21 7h-2V5h-2v2h-2v2h2v2h2V9h2zM5 7a4 4 0 1 0 0 8A4 4 0 0 0 5 7zm0 6a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm8 4H1v-1c0-2.21 1.79-4 4-4h6c2.21 0 4 1.79 4 4v1z"/></svg>
                </div>
                <div class="ls-feat-text">
                    <div class="ls-feat-title">User & Role Management</div>
                    <div class="ls-feat-sub">Assign projects and balances per user</div>
                </div>
            </div>
            <div class="ls-feat">
                <div class="ls-feat-icon">
                    <svg viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                </div>
                <div class="ls-feat-text">
                    <div class="ls-feat-title">Transfer & Expense Tracking</div>
                    <div class="ls-feat-sub">Real-time balance and debit monitoring</div>
                </div>
            </div>
            <div class="ls-feat">
                <div class="ls-feat-icon">
                    <svg viewBox="0 0 24 24"><path d="M19 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2zm-7 3a3 3 0 1 1 0 6 3 3 0 0 1 0-6zm6 12H6v-.57c0-2 4-3.1 6-3.1s6 1.1 6 3.1V18z"/></svg>
                </div>
                <div class="ls-feat-text">
                    <div class="ls-feat-title">Activity Logs</div>
                    <div class="ls-feat-sub">Full audit trail of every action</div>
                </div>
            </div>
        </div>

        {{-- Stats strip --}}
        <div class="ls-stats">
            <div class="ls-stat">
                <div class="ls-stat-num">{{ number_format(rand(50,150)) }}<span>+</span></div>
                <div class="ls-stat-lbl">Users</div>
            </div>
            <div class="ls-stat-div"></div>
            <div class="ls-stat">
                <div class="ls-stat-num">{{ number_format(rand(20,80)) }}<span>+</span></div>
                <div class="ls-stat-lbl">Projects</div>
            </div>
            <div class="ls-stat-div"></div>
            <div class="ls-stat">
                <div class="ls-stat-num">{{ number_format(rand(200,500)) }}<span>+</span></div>
                <div class="ls-stat-lbl">Expenses</div>
            </div>
        </div>
    </div>

    {{-- ══ RIGHT PANEL ══ --}}
    <div class="ls-r">
        <div class="ls-prog"></div>

        {{-- Mobile logo --}}
        <div class="ls-mob-logo">
            <div class="mob-icon">
                <svg viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 0 0 20A10 10 0 0 0 12 2zm-1 14H9V10h2v6zm0-8H9V6h2v2zm4 8h-2v-4h2v4zm0-6h-2v-2h2v2z"/></svg>
            </div>
            <span>{{ config('app.name', 'Expense Manager') }}</span>
        </div>

        @if (session('status'))
            <div class="ls-status">
                <svg width="14" height="14" fill="#006666" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                {{ session('status') }}
            </div>
        @endif

        {{-- Heading --}}
        <div class="ls-rhead">
            <h1 class="ls-rtitle">Welcome back</h1>
            <span class="ls-rbar"></span>
            <p class="ls-rsub">Sign in to your account to continue.</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Email --}}
            <div class="ls-fld">
                <label class="ls-lbl" for="email">Email address</label>
                <div class="ls-fw">
                    <span class="ls-ico">
                        <svg viewBox="0 0 24 24"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 2-8 5-8-5h16zm0 12H4V9l8 5 8-5v9z"/></svg>
                    </span>
                    <input id="email" type="email" name="email"
                           value="{{ old('email') }}"
                           class="ls-inp"
                           placeholder="you@company.com"
                           required autofocus autocomplete="username">
                </div>
                @error('email')
                    <p class="ls-err">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div class="ls-fld">
                <label class="ls-lbl" for="password">Password</label>
                <div class="ls-fw">
                    <span class="ls-ico">
                        <svg viewBox="0 0 24 24"><path d="M12 1a7 7 0 0 0-7 7v2H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2h-1V8a7 7 0 0 0-7-7zm0 2a5 5 0 0 1 5 5v2H7V8a5 5 0 0 1 5-5zm0 10a2 2 0 1 1 0 4 2 2 0 0 1 0-4z"/></svg>
                    </span>
                    <input id="password" type="password" name="password"
                           class="ls-inp" placeholder="••••••••"
                           required autocomplete="current-password">
                    <button type="button" class="ls-eye" onclick="
                        var p=document.getElementById('password');
                        var s=this.querySelectorAll('svg');
                        if(p.type==='password'){
                            p.type='text';
                            s[0].style.display='none';
                            s[1].style.display='block';
                        } else {
                            p.type='password';
                            s[0].style.display='block';
                            s[1].style.display='none';
                        }
                    ">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5C21.27 7.61 17 4.5 12 4.5zM12 17a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-8a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/>
                        </svg>
                        <svg viewBox="0 0 24 24" style="display:none">
                            <path d="M2 5.27L3.28 4 20 20.72 18.73 22l-3.08-3.08A10.49 10.49 0 0 1 12 19.5C7 19.5 2.73 16.39 1 12a10.44 10.44 0 0 1 4.35-5.38L2 5.27zM12 6a5 5 0 0 1 4.9 4.07L11.93 5A4.97 4.97 0 0 1 12 6zm0 11a5 5 0 0 1-4.9-4.07L12.07 18A5 5 0 0 1 12 17z"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="ls-err">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember --}}
            <div class="ls-rem">
                <input id="remember_me" type="checkbox" name="remember">
                <label for="remember_me">{{ __('Keep me signed in') }}</label>
            </div>

            {{-- Submit --}}
            <button type="submit" class="ls-btn">
                {{ __('Sign In') }}
                <svg class="arrow" width="15" height="15" fill="#fff" viewBox="0 0 24 24">
                    <path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z"/>
                </svg>
            </button>

            @if (Route::has('password.request'))
            <div class="ls-foot">
                <div class="ls-sep"><span>or</span></div>
                <a class="ls-forgot" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            </div>
            @endif
        </form>
    </div>

</div>
</body>
</html>