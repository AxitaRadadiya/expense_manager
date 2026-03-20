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
           THEME  —  Deep Navy + Gold Amber
           Primary  #C9960C  |  Bright #F5BE2E
           Panel    #0B1120 → #111C30
        ═══════════════════════════════════════════ */
        :root {
            --pri    : #C9960C;
            --pri-lt : #F5BE2E;
            --pri-dk : #9A6E00;
            --dark   : #0B1120;
            --dark-2 : #111C30;
            --ink    : #0D1A30;
            --ink-2  : #334155;
            --ink-3  : #64748b;
            --ink-4  : #94a3b8;
            --surf   : #F0F2F7;
            --card   : #ffffff;
            --bdr    : #e2e8f0;
            --radius : 10px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; font-family: "DM Sans", -apple-system, sans-serif; }

        body {
            background: var(--surf);
            min-height: 100vh;
            display: flex;
            align-items: stretch;
        }

        ::-webkit-scrollbar            { width: 5px; }
        ::-webkit-scrollbar-track      { background: #0B1120; }
        ::-webkit-scrollbar-thumb      { background: #2a3a58; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover{ background: var(--pri); }

        /* ══ SHELL ══ */
        .ls { display: flex; width: 100%; min-height: 100vh; }

        /* ══ LEFT PANEL ══ */
        .ls-l {
            flex: 1;
            display: none;
            flex-direction: column;
            justify-content: space-between;
            padding: 52px 52px;
            background: linear-gradient(160deg, var(--dark) 0%, #0e1a2e 50%, #121f38 100%);
            position: relative;
            overflow: hidden;
        }
        @media (min-width: 1024px) { .ls-l { display: flex; } }

        /* Dot grid */
        .ls-grid {
            position: absolute; inset: 0; pointer-events: none;
            background-image: radial-gradient(rgba(201,150,12,.2) 1px, transparent 1px);
            background-size: 32px 32px;
        }

        /* Gold glow blobs */
        .ls-blob { position: absolute; border-radius: 50%; pointer-events: none; }
        .ls-blob-a {
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(201,150,12,.2) 0%, transparent 65%);
            top: -120px; right: -80px;
        }
        .ls-blob-b {
            width: 240px; height: 240px;
            background: radial-gradient(circle, rgba(245,190,46,.1) 0%, transparent 70%);
            bottom: 40px; left: -50px;
        }

        /* Brand */
        .ls-brand { position: relative; z-index: 2; display: flex; align-items: center; gap: 12px; }
        .ls-brand-icon {
            width: 42px; height: 42px; border-radius: 10px;
            border: 2px solid var(--pri);
            background: rgba(201,150,12,.15);
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .ls-brand-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.15rem; font-weight: 800; color: #fff;
        }

        /* Hero */
        .ls-hero { position: relative; z-index: 2; }

        .ls-badge {
            display: inline-flex; align-items: center; gap: 7px;
            background: rgba(201,150,12,.15);
            border: 1px solid rgba(201,150,12,.3);
            border-radius: 20px; padding: 5px 14px; margin-bottom: 22px;
        }
        .ls-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--pri-lt); animation: blink 2s ease-in-out infinite; }
        @keyframes blink { 0%,100%{opacity:1;} 50%{opacity:.3;} }
        .ls-badge span { font-size: .68rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: var(--pri-lt); }

        .ls-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.7rem; font-weight: 700; line-height: 1.1;
            color: rgba(255,255,255,.9); letter-spacing: -.02em; margin-bottom: 16px;
        }
        .ls-title em { font-style: normal; color: var(--pri-lt); }

        .ls-desc {
            font-size: .88rem; font-weight: 300;
            color: rgba(255,255,255,.3); line-height: 1.9; max-width: 300px;
        }

        /* Stats strip */
        .ls-stats {
            position: relative; z-index: 2;
            display: flex; align-items: center;
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(201,150,12,.15);
            border-radius: var(--radius); padding: 20px 24px;
        }
        .ls-stat { flex: 1; text-align: center; }
        .ls-stat-num {
            font-family: 'Playfair Display', serif;
            font-size: 1.85rem; font-weight: 700; color: #fff; line-height: 1;
        }
        .ls-stat-num span { color: var(--pri-lt); font-size: 1.2rem; }
        .ls-stat-lbl { font-size: .68rem; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; color: rgba(255,255,255,.3); margin-top: 5px; }
        .ls-stat-div { width: 1px; height: 36px; background: rgba(201,150,12,.2); flex-shrink: 0; }

        /* ══ RIGHT PANEL ══ */
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

        /* Gold top accent */
        .ls-r::before {
            content: '';
            position: absolute; top: 0; left: 50%; transform: translateX(-50%);
            width: 60px; height: 3px;
            background: linear-gradient(90deg, var(--pri-dk), var(--pri-lt));
            border-radius: 0 0 4px 4px;
        }

        /* Mobile logo */
        .ls-mob-logo { display: none; justify-content: center; margin-bottom: 36px; }
        @media (max-width: 1023px) { .ls-mob-logo { display: flex; } }
        .ls-mob-logo span {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem; font-weight: 800; color: var(--ink);
        }

        /* Session status */
        .ls-status {
            background: #f0fdf4; border-left: 3px solid #16a34a;
            border-radius: 6px; padding: .7rem 1rem;
            font-size: .83rem; color: #15803d;
            margin-bottom: 1.2rem;
        }

        /* Heading */
        .ls-rhead { margin-bottom: 2rem; }
        .ls-rtitle {
            font-family: 'Playfair Display', serif;
            font-size: 1.9rem; font-weight: 800;
            color: var(--ink); margin-bottom: .5rem;
        }
        .ls-rbar {
            display: block; width: 36px; height: 3px;
            background: linear-gradient(90deg, var(--pri-dk), var(--pri-lt));
            border-radius: 2px; margin-bottom: .65rem;
        }
        .ls-rsub { font-size: .87rem; color: var(--ink-3); font-weight: 400; }

        /* Fields */
        .ls-fld { margin-bottom: 1.2rem; }
        .ls-lbl { display: block; font-size: .78rem; font-weight: 700; color: var(--ink-2); margin-bottom: .45rem; letter-spacing: .02em; }

        .ls-fw {
            position: relative;
            display: flex; align-items: center;
        }
        .ls-ico {
            position: absolute; left: 13px;
            display: flex; align-items: center;
            pointer-events: none;
        }
        .ls-ico svg { width: 16px; height: 16px; fill: var(--ink-4); }

        .ls-inp {
            width: 100%; height: 44px;
            border: 1.5px solid var(--bdr);
            border-radius: 8px;
            padding: 0 2.6rem 0 2.6rem;
            font-size: .88rem; font-family: "DM Sans", sans-serif;
            color: var(--ink); background: #fff;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .ls-inp:focus {
            border-color: var(--pri);
            box-shadow: 0 0 0 3px rgba(201,150,12,.15);
        }
        .ls-inp::placeholder { color: var(--ink-4); }

        .ls-eye {
            position: absolute; right: 12px;
            background: none; border: none; cursor: pointer;
            padding: 0; display: flex; align-items: center;
        }
        .ls-eye svg { width: 16px; height: 16px; fill: var(--ink-4); }
        .ls-eye:hover svg { fill: var(--pri); }

        /* Error */
        .ls-err { font-size: .78rem; color: #dc2626; margin-top: .35rem; }

        /* Remember */
        .ls-rem {
            display: flex; align-items: center; gap: .5rem;
            margin-bottom: 1.5rem;
        }
        .ls-rem input[type="checkbox"] { accent-color: var(--pri); width:15px; height:15px; }
        .ls-rem label { font-size: .82rem; color: var(--ink-3); cursor: pointer; margin: 0; }

        /* Submit */
        .ls-btn {
            width: 100%; height: 46px;
            background: linear-gradient(135deg, var(--pri-dk), var(--pri-lt));
            border: none; border-radius: 8px;
            color: #111; font-family: "DM Sans", sans-serif;
            font-size: .9rem; font-weight: 700; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            box-shadow: 0 4px 18px rgba(201,150,12,.3);
            transition: transform .18s, box-shadow .18s;
            margin-bottom: 16px;
        }
        .ls-btn:hover { transform: translateY(-2px); box-shadow: 0 7px 26px rgba(201,150,12,.4); }
        .ls-btn:active { transform: none; }
        .ls-btn .arrow { transition: transform .2s; }
        .ls-btn:hover .arrow { transform: translateX(3px); }

        /* Forgot */
        .ls-foot { text-align: center; }
        .ls-sep { display: flex; align-items: center; gap: 12px; margin: 4px 0 16px; }
        .ls-sep::before, .ls-sep::after { content:''; flex:1; height:1px; background:var(--bdr); }
        .ls-sep span { font-size: .7rem; color: var(--ink-4); }
        .ls-forgot {
            font-size: .82rem; color: var(--ink-3);
            text-decoration: none; font-weight: 600; transition: color .2s;
        }
        .ls-forgot:hover { color: var(--pri); }
    </style>
</head>
<body>

<div class="ls">

    {{-- ══ LEFT PANEL ══ --}}
    <div class="ls-l">
        <div class="ls-grid"></div>
        <div class="ls-blob ls-blob-a"></div>
        <div class="ls-blob ls-blob-b"></div>

        {{-- Brand --}}
        <div class="ls-brand">
            <div class="ls-brand-icon">
                <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:var(--pri-lt)">
                    <path d="M19 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2zm-7 14H7v-2h5v2zm5-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                </svg>
            </div>
            <span class="ls-brand-name">{{ config('app.name', 'PrintPro') }}</span>
        </div>

        {{-- Hero --}}
        <div class="ls-hero">
            <div class="ls-badge">
                <div class="ls-dot"></div>
                <span>Print Management System</span>
            </div>
            <h2 class="ls-title">
                Manage every<br>
                print job with<br>
                <em>precision & ease.</em>
            </h2>
            <p class="ls-desc">
                Track job cards, monitor statuses, manage contacts,
                and keep your print workflow running smoothly.
            </p>
        </div>

        {{-- Stats strip --}}
        <div class="ls-stats">
            <div class="ls-stat">
                <div class="ls-stat-num">{{ number_format(rand(100,200)) }}<span>+</span></div>
                <div class="ls-stat-lbl">Job Cards</div>
            </div>
            <div class="ls-stat-div"></div>
            <div class="ls-stat">
                <div class="ls-stat-num">{{ number_format(rand(40,80)) }}<span>+</span></div>
                <div class="ls-stat-lbl">Active Jobs</div>
            </div>
            <div class="ls-stat-div"></div>
            <div class="ls-stat">
                <div class="ls-stat-num">{{ number_format(rand(20,50)) }}<span>+</span></div>
                <div class="ls-stat-lbl">Contacts</div>
            </div>
        </div>
    </div>

    {{-- ══ RIGHT PANEL ══ --}}
    <div class="ls-r">

        <div class="ls-mob-logo">
            <span>{{ config('app.name') }}</span>
        </div>

        @if (session('status'))
            <div class="ls-status">{{ session('status') }}</div>
        @endif

        <div class="ls-rhead">
            <h1 class="ls-rtitle">Welcome back</h1>
            <span class="ls-rbar"></span>
            <p class="ls-rsub">Sign in to your admin account to continue.</p>
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
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                           class="ls-inp" placeholder="you@company.com"
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
                        if(p.type==='password'){p.type='text';s[0].style.display='none';s[1].style.display='block';}
                        else{p.type='password';s[0].style.display='block';s[1].style.display='none';}
                    ">
                        <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5C21.27 7.61 17 4.5 12 4.5zM12 17a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-8a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/></svg>
                        <svg viewBox="0 0 24 24" style="display:none"><path d="M2 5.27L3.28 4 20 20.72 18.73 22l-3.08-3.08A10.49 10.49 0 0 1 12 19.5C7 19.5 2.73 16.39 1 12a10.44 10.44 0 0 1 4.35-5.38L2 5.27zM12 6a5 5 0 0 1 4.9 4.07L11.93 5A4.97 4.97 0 0 1 12 6zm0 11a5 5 0 0 1-4.9-4.07L12.07 18A5 5 0 0 1 12 17z"/></svg>
                    </button>
                </div>
                @error('password')
                    <p class="ls-err">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember --}}
            <div class="ls-rem">
                <input id="remember_me" type="checkbox" name="remember">
                <label for="remember_me">{{ __('Remember me') }}</label>
            </div>

            {{-- Submit --}}
            <button type="submit" class="ls-btn">
                {{ __('Sign in') }}
                <svg class="arrow" width="15" height="15" fill="#111" viewBox="0 0 24 24"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z"/></svg>
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