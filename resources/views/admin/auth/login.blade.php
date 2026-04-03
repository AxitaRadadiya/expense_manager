<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'App') }} — Sign In</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            height: 100%;
            font-family: "DM Sans", -apple-system, sans-serif;
            background: #f0f6f6;
        }

        .page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            border: 1px solid #c8e6e6;
            border-radius: 12px;
            padding: 40px 36px;
            position: relative;
            overflow: hidden;
        }
        @media (max-width: 480px) { .card { padding: 32px 24px; } }

        /* Teal top accent */
        .card::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, #006666, #00b5b5);
        }

        /* Brand icon + heading */
        .brand {
            text-align: center;
            margin-bottom: 28px;
        }
        .brand-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, #006666, #00b5b5);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 14px;
        }
        .brand-icon svg { width: 22px; height: 22px; fill: #fff; }

        .brand h1 {
            font-size: 1.4rem; font-weight: 700;
            color: #0d2e2e; margin-bottom: 6px;
        }
        .brand p {
            font-size: .85rem; color: #5a8080;
        }

        /* Status messages */
        .status-success {
            background: #e0f7f7; border-left: 3px solid #008d8d;
            border-radius: 8px; padding: .75rem 1rem;
            font-size: .82rem; color: #006666;
            margin-bottom: 1.2rem;
            display: flex; align-items: center; gap: .5rem;
        }
        .status-warn {
            background: #fff7ed; border-left: 3px solid #f59e0b;
            border-radius: 8px; padding: .75rem 1rem;
            font-size: .82rem; color: #92400e;
            margin-bottom: 1.2rem;
            display: flex; align-items: flex-start; gap: .5rem;
            line-height: 1.5;
        }
        .status-warn svg { flex-shrink: 0; margin-top: 1px; }

        /* Form fields */
        .field { margin-bottom: 16px; }
        .field label {
            display: block;
            font-size: .78rem; font-weight: 700;
            color: #2a5050; margin-bottom: 6px; letter-spacing: .02em;
        }
        .input-wrap { position: relative; display: flex; align-items: center; }
        .input-icon {
            position: absolute; left: 11px; pointer-events: none;
            display: flex; align-items: center;
        }
        .input-icon svg { width: 15px; height: 15px; fill: #a0cece; }

        .input-wrap input {
            width: 100%; height: 44px;
            border: 1.5px solid #c8e6e6;
            border-radius: 8px;
            padding: 0 40px 0 36px;
            font-size: .88rem; font-family: "DM Sans", sans-serif;
            color: #0d2e2e; background: #fff;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .input-wrap input:focus {
            border-color: #008d8d;
            box-shadow: 0 0 0 3px rgba(0,141,141,.12);
        }
        .input-wrap input::placeholder { color: #a0cece; }

        /* Eye toggle */
        .eye-btn {
            position: absolute; right: 11px;
            background: none; border: none; cursor: pointer;
            padding: 0; display: flex; align-items: center;
        }
        .eye-btn svg { width: 16px; height: 16px; fill: #a0cece; transition: fill .18s; }
        .eye-btn:hover svg { fill: #008d8d; }

        /* Validation errors */
        .err {
            font-size: .76rem; color: #dc2626;
            margin-top: 5px;
            display: flex; align-items: center; gap: 4px;
        }
        .err::before { content: '⚠'; font-size: .7rem; }

        /* Remember + forgot row */
        .meta-row {
            display: flex; align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .remember {
            display: flex; align-items: center; gap: 6px;
            font-size: .82rem; color: #5a8080; cursor: pointer;
        }
        .remember input[type="checkbox"] {
            accent-color: #008d8d; width: 14px; height: 14px;
        }
        .forgot {
            font-size: .82rem; color: #008d8d;
            text-decoration: none; font-weight: 600;
            transition: color .2s;
        }
        .forgot:hover { color: #006666; }

        /* Submit button */
        .btn-submit {
            width: 100%; height: 46px;
            background: linear-gradient(135deg, #006666, #00a8a8);
            border: none; border-radius: 8px;
            color: #fff; font-family: "DM Sans", sans-serif;
            font-size: .9rem; font-weight: 700;
            cursor: pointer; letter-spacing: .02em;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            box-shadow: 0 4px 16px rgba(0,141,141,.25);
            transition: transform .18s, box-shadow .18s;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,141,141,.35);
        }
        .btn-submit:active { transform: none; }
        .btn-submit .arrow { transition: transform .2s; }
        .btn-submit:hover .arrow { transform: translateX(3px); }
    </style>
</head>
<body>

<div class="page">
    <div class="card">

        {{-- Brand --}}
        <div class="brand">
            <div class="brand-icon">
                <svg viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 0 0 20A10 10 0 0 0 12 2zm-1 14H9V10h2v6zm0-8H9V6h2v2zm4 8h-2v-4h2v4zm0-6h-2v-2h2v2z"/></svg>
            </div>
            <h1>Welcome back</h1>
            <p>Sign in to your account to continue.</p>
        </div>

        {{-- Session status --}}
        @if (session('status') && str_contains(session('status'), 'another device'))
            <div class="status-warn">
                <svg width="15" height="15" fill="#f59e0b" viewBox="0 0 24 24"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
                <span>{{ session('status') }}</span>
            </div>
        @elseif (session('status'))
            <div class="status-success">
                <svg width="14" height="14" fill="#006666" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Email --}}
            <div class="field">
                <label for="email">Email address</label>
                <div class="input-wrap">
                    <span class="input-icon">
                        <svg viewBox="0 0 24 24"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 2-8 5-8-5h16zm0 12H4V9l8 5 8-5v9z"/></svg>
                    </span>
                    <input id="email" type="email" name="email"
                           value="{{ old('email') }}"
                           placeholder="you@company.com"
                           required autofocus autocomplete="username">
                </div>
                @error('email')
                    <p class="err">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div class="field">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <span class="input-icon">
                        <svg viewBox="0 0 24 24"><path d="M12 1a7 7 0 0 0-7 7v2H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2h-1V8a7 7 0 0 0-7-7zm0 2a5 5 0 0 1 5 5v2H7V8a5 5 0 0 1 5-5zm0 10a2 2 0 1 1 0 4 2 2 0 0 1 0-4z"/></svg>
                    </span>
                    <input id="password" type="password" name="password"
                           placeholder="••••••••"
                           required autocomplete="current-password">
                    <button type="button" class="eye-btn" onclick="
                        var p = document.getElementById('password');
                        var icons = this.querySelectorAll('svg');
                        if (p.type === 'password') {
                            p.type = 'text';
                            icons[0].style.display = 'none';
                            icons[1].style.display = 'block';
                        } else {
                            p.type = 'password';
                            icons[0].style.display = 'block';
                            icons[1].style.display = 'none';
                        }
                    ">
                        <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5C21.27 7.61 17 4.5 12 4.5zM12 17a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-8a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/></svg>
                        <svg viewBox="0 0 24 24" style="display:none"><path d="M2 5.27L3.28 4 20 20.72 18.73 22l-3.08-3.08A10.49 10.49 0 0 1 12 19.5C7 19.5 2.73 16.39 1 12a10.44 10.44 0 0 1 4.35-5.38L2 5.27zM12 6a5 5 0 0 1 4.9 4.07L11.93 5A4.97 4.97 0 0 1 12 6zm0 11a5 5 0 0 1-4.9-4.07L12.07 18A5 5 0 0 1 12 17z"/></svg>
                    </button>
                </div>
                @error('password')
                    <p class="err">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember + Forgot --}}
            <div class="meta-row">
                <label class="remember">
                    <input type="checkbox" name="remember" id="remember_me">
                    Keep me signed in
                </label>
                @if (Route::has('password.request'))
                    <a class="forgot" href="{{ route('password.request') }}">Forgot password?</a>
                @endif
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-submit">
                Sign In
                <svg class="arrow" width="15" height="15" fill="#fff" viewBox="0 0 24 24"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z"/></svg>
            </button>

        </form>
    </div>
</div>

</body>
</html>