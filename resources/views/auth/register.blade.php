@php $hasLogin = \Illuminate\Support\Facades\Route::has('login'); @endphp
    <!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>Kayıt Ol • HAKLO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* === Shared HAKLO Auth Theme (same as login) === */
        .hak-login{--bg:#0e1116;--card:#fff;--ink:#0f172a;--muted:#6b7280;--brand:#1565d8;--brand-ink:#0b4aa8;--ring:rgba(21,101,216,.25);min-height:100svh;display:grid;place-items:center;background:radial-gradient(1200px 800px at 10% -20%,#2a3a81 0,transparent 45%),radial-gradient(1200px 800px at 110% 120%,#0ea5e9 0,transparent 45%),linear-gradient(160deg,#0b1220,#0e1116)}
        .hak-login .shell{width:min(1100px,96vw);display:grid;grid-template-columns:1.1fr .9fr;border-radius:20px;overflow:hidden;box-shadow:0 40px 120px rgba(0,0,0,.35),0 0 0 1px rgba(255,255,255,.05) inset}
        @media(max-width:900px){.hak-login .shell{grid-template-columns:1fr}.hak-login .side{display:none}}
        .hak-login .side{position:relative;background:linear-gradient(200deg,#0f1b3d,#111827 60%);color:#cbd5e1;padding:48px}
        .hak-login .side .logo{display:flex;align-items:center;gap:12px;font-weight:700;letter-spacing:.4px;color:#e5e7eb}
        .hak-login .side .h{width:40px;height:40px;display:grid;place-items:center;border-radius:12px;background:linear-gradient(135deg,#1d4ed8,#0ea5e9);color:#fff;font-weight:800}
        .hak-login .side h2{margin:28px 0 12px;font-size:28px;color:#e2e8f0}
        .hak-login .side p{color:#93a3b8;line-height:1.6;max-width:36ch}
        .hak-login .side .illus{position:absolute;right:-40px;bottom:-40px;width:380px;height:380px;background:radial-gradient(closest-side,rgba(14,165,233,.25),transparent 70%),conic-gradient(from 180deg at 50% 50%,rgba(21,101,216,.65),transparent 70%);filter:blur(12px);opacity:.7}
        .hak-login .card{background:var(--card);color:var(--ink);padding:34px;display:grid;gap:18px}
        .hak-login .head h1{font-size:24px;margin:0 0 6px}
        .hak-login .sub{color:var(--muted);font-size:14px}
        .hak-login .alert{font-size:14px;padding:10px 12px;border-radius:10px;border:1px solid}
        .hak-login .alert.err{background:#fef2f2;border-color:#fecaca;color:#b91c1c}
        .hak-login form{display:grid;gap:14px}
        .hak-login label{font-size:13px;color:#334155;display:block;margin-bottom:6px}
        .hak-login .input{width:100%;border:1px solid #e5e7eb;border-radius:12px;padding:12px 14px;font-size:15px;outline:0;transition:box-shadow .15s,border-color .15s;background:#fff}
        .hak-login .input:focus{border-color:var(--brand);box-shadow:0 0 0 4px var(--ring)}
        .hak-login .field{position:relative}
        .hak-login .toggle-eye{position:absolute;right:10px;top:50%;transform:translateY(-50%);border:none;background:transparent;cursor:pointer;font-size:14px;color:#64748b}
        .hak-login .row{display:flex;justify-content:space-between;align-items:center;gap:12px}
        .hak-login .link{color:var(--brand);text-decoration:none;font-size:14px}
        .hak-login .link:hover{text-decoration:underline}
        .hak-login .btn{width:100%;border:none;border-radius:12px;padding:12px 16px;background:linear-gradient(135deg,var(--brand),#1d9bf0);color:#fff;font-weight:600;font-size:15px;cursor:pointer;transition:transform .06s,box-shadow .2s,filter .2s;box-shadow:0 10px 30px rgba(21,101,216,.35)}
        .hak-login .btn:hover{filter:brightness(1.05)} .hak-login .btn:active{transform:translateY(1px)}
        .hak-login .brand-mini{display:flex;align-items:center;gap:10px;margin-top:4px;color:#64748b}
        .hak-login .brand-mini .h{width:28px;height:28px;background:linear-gradient(135deg,#1d4ed8,#0ea5e9);color:#fff;display:grid;place-items:center;border-radius:8px;font-weight:800}
    </style>
</head>
<body class="hak-login">
<div class="shell">
    <aside class="side">
        <div class="logo"><span class="h">H</span> HAKLO</div>
        <h2>Aramıza katıl 🚀</h2>
        <p>Üye olarak sepetini sakla, siparişlerini takip et ve kişiye özel kampanyaları yakala.</p>
        <div class="illus" aria-hidden="true"></div>
    </aside>

    <section class="card">
        <div class="head">
            <h1>Kayıt ol</h1>
            <div class="sub">Zaten hesabın var mı?
                @if($hasLogin) <a class="link" href="{{ route('login') }}">Giriş yap</a> @endif
            </div>
        </div>

        @if ($errors->any())
            <div class="alert err">@foreach ($errors->all() as $err)<div>{{ $err }}</div>@endforeach</div>
        @endif

        <form method="POST" action="{{ route('register') }}" novalidate>
            @csrf
            <div>
                <label for="name">Ad Soyad</label>
                <input id="name" name="name" type="text" class="input" value="{{ old('name') }}" required autofocus placeholder="Adın Soyadın">
            </div>
            <div>
                <label for="email">E-posta</label>
                <input id="email" name="email" type="email" class="input" value="{{ old('email') }}" required placeholder="ornek@haklo.com">
            </div>
            <div class="field">
                <label for="password">Şifre</label>
                <input id="password" name="password" type="password" class="input" required placeholder="••••••••">
                <button type="button" class="toggle-eye" onclick="const i=document.getElementById('password'); i.type=i.type==='password'?'text':'password'">Göster</button>
            </div>
            <div class="field">
                <label for="password_confirmation">Şifre (tekrar)</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="input" required placeholder="••••••••">
            </div>

            <button class="btn" type="submit">Hesap oluştur</button>

            <div class="brand-mini"><span class="h">H</span><span>Kaydını saniyeler içinde tamamla</span></div>
        </form>
    </section>
</div>
</body>
</html>
