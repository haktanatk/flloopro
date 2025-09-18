{{-- resources/views/auth/login.blade.php --}}
@php
    $hasForgot = \Illuminate\Support\Facades\Route::has('password.request');
@endphp
    <!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>Giriş Yap • HAKLO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* ====== HAKLO Login (scoped) ====== */
        .hak-login { --bg:#0e1116; --card:#ffffff; --ink:#0f172a; --muted:#6b7280; --brand:#1565d8; --brand-ink:#0b4aa8; --ring: rgba(21,101,216,.25); --ok:#14b8a6; --warn:#f59e0b; --err:#ef4444; isolation:isolate; min-height:100svh; display:grid; place-items:center; background: radial-gradient(1200px 800px at 10% -20%, #2a3a81 0%, transparent 45%), radial-gradient(1200px 800px at 110% 120%, #0ea5e9 0%, transparent 45%), linear-gradient(160deg, #0b1220, #0e1116); }
        .hak-login .shell{ width:min(1100px,96vw); display:grid; grid-template-columns: 1.1fr .9fr; gap:0; background:transparent; border-radius:20px; overflow:hidden; box-shadow: 0 40px 120px rgba(0,0,0,.35), 0 0 0 1px rgba(255,255,255,.05) inset;}
        @media (max-width: 900px){ .hak-login .shell{ grid-template-columns: 1fr; } .hak-login .side{ display:none; } }

        /* Left brand side */
        .hak-login .side{ position:relative; background: linear-gradient(200deg, #0f1b3d, #111827 60%); color:#cbd5e1; padding:48px; }
        .hak-login .side .logo{ display:flex; align-items:center; gap:12px; font-weight:700; letter-spacing:.4px; color:#e5e7eb; }
        .hak-login .side .logo .h{ width:40px; height:40px; display:grid; place-items:center; border-radius:12px; background: linear-gradient(135deg, #1d4ed8, #0ea5e9); color:#fff; font-weight:800; }
        .hak-login .side h2{ margin:28px 0 12px; font-size:28px; color:#e2e8f0; }
        .hak-login .side p{ color:#93a3b8; line-height:1.6; max-width:36ch; }
        .hak-login .side .illus{ position:absolute; right:-40px; bottom:-40px; width:380px; height:380px; background: radial-gradient(closest-side, rgba(14,165,233,.25), transparent 70%), conic-gradient(from 180deg at 50% 50%, rgba(21,101,216,.65), transparent 70%); filter: blur(12px); opacity:.7; }

        /* Right form card */
        .hak-login .card{ background:var(--card); color:var(--ink); padding:34px; display:grid; gap:18px; position:relative; }
        .hak-login .card .head{ margin-bottom:4px; }
        .hak-login .card h1{ font-size:24px; margin:0 0 6px; }
        .hak-login .card .sub{ color:var(--muted); font-size:14px; }
        .hak-login .alert{ font-size:14px; padding:10px 12px; border-radius:10px; border:1px solid; }
        .hak-login .alert.ok{ background:#ecfdf5; border-color:#99f6e4; color:#0f766e; }
        .hak-login .alert.err{ background:#fef2f2; border-color:#fecaca; color:#b91c1c; }

        .hak-login form{ display:grid; gap:14px; }
        .hak-login label{ font-size:13px; color:#334155; display:block; margin-bottom:6px; }
        .hak-login .input{ width:100%; border:1px solid #e5e7eb; border-radius:12px; padding:12px 14px; font-size:15px; outline:none; transition: box-shadow .15s, border-color .15s; background:#fff; }
        .hak-login .input:focus{ border-color:var(--brand); box-shadow: 0 0 0 4px var(--ring); }
        .hak-login .field{ position:relative; }
        .hak-login .toggle-eye{ position:absolute; right:10px; top:50%; transform:translateY(-50%); border:none; background:transparent; cursor:pointer; font-size:14px; color:#64748b; }

        .hak-login .row{ display:flex; justify-content:space-between; align-items:center; gap:12px; }
        .hak-login .remember{ display:flex; align-items:center; gap:8px; font-size:14px; color:#475569; }
        .hak-login .link{ color:var(--brand); text-decoration:none; font-size:14px; }
        .hak-login .link:hover{ text-decoration:underline; }

        .hak-login .btn{ width:100%; border:none; border-radius:12px; padding:12px 16px; background:linear-gradient(135deg, var(--brand), #1d9bf0); color:#fff; font-weight:600; font-size:15px; cursor:pointer; transition: transform .06s ease, box-shadow .2s ease, filter .2s ease; box-shadow: 0 10px 30px rgba(21,101,216,.35); }
        .hak-login .btn:hover{ filter:brightness(1.05); }
        .hak-login .btn:active{ transform: translateY(1px); }

        .hak-login .meta{ text-align:center; font-size:14px; color:#64748b; margin-top:4px; }
        .hak-login .divider{ display:grid; grid-template-columns:1fr auto 1fr; align-items:center; gap:10px; color:#94a3b8; font-size:13px; }
        .hak-login .divider::before, .hak-login .divider::after{ content:""; height:1px; background:#e5e7eb; }

        .hak-login .brand-mini{ display:flex; align-items:center; gap:10px; margin-top:8px; }
        .hak-login .brand-mini .h{ width:28px; height:28px; background: linear-gradient(135deg, #1d4ed8, #0ea5e9); color:#fff; display:grid; place-items:center; border-radius:8px; font-weight:800; }
    </style>
</head>
<body class="hak-login">

<div class="shell">
    {{-- Sol marka/mesaj bölümü --}}
    <aside class="side">
        <div class="logo"><span class="h">H</span> HAKLO</div>
        <h2>Tekrar hoş geldin 👋</h2>
        <p>Hesabına giriş yap, sepetin kaldığı yerden devam etsin. Güvenli ve hızlı alışveriş deneyimi için yalnızca birkaç saniye.</p>
        <div class="illus" aria-hidden="true"></div>
    </aside>

    {{-- Sağ: Form kartı --}}
    <section class="card">
        <div class="head">
            <h1>Giriş yap</h1>
            <div class="sub">Hesabın yok mu? <a class="link" href="{{ route('register') }}">Hemen kaydol</a></div>
        </div>

        {{-- Status / Hata mesajları --}}
        @if (session('status'))
            <div class="alert ok">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert err">
                @foreach ($errors->all() as $err)
                    <div>{{ $err }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            <div>
                <label for="email">E-posta</label>
                <input id="email" name="email" type="email" class="input" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="ornek@haklo.com">
            </div>

            <div class="field">
                <label for="password">Şifre</label>
                <input id="password" name="password" type="password" class="input" required autocomplete="current-password" placeholder="••••••••">
                <button type="button" class="toggle-eye" onclick="const i=document.getElementById('password'); i.type = i.type==='password' ? 'text':'password'">
                    Göster
                </button>
            </div>

            <div class="row">
                <label class="remember">
                    <input type="checkbox" name="remember"> Beni hatırla
                </label>
                @if($hasForgot)
                    <a class="link" href="{{ route('password.request') }}">Şifremi unuttum</a>
                @endif
            </div>

            <button class="btn" type="submit">Giriş yap</button>

            <div class="meta">
                <div class="brand-mini">
                    <span class="h">H</span>
                    <span>HAKLO ile güvenli oturum</span>
                </div>
            </div>
        </form>

        <div class="divider">veya</div>

        {{-- İstersen sosyal butonlar ekleyebilirsin (placeholder) --}}
        <form onsubmit="event.preventDefault();" aria-hidden="true">
            <button class="btn" type="button" style="background:#111827; box-shadow:none;">Google ile giriş (yakında)</button>
        </form>
    </section>
</div>

</body>
</html>
