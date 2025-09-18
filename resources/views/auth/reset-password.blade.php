<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>Yeni Şifre Belirle • HAKLO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="hak-login">
<style>
    /* same CSS as register/login */
</style>
<div class="shell">
    <aside class="side">
        <div class="logo"><span class="h">H</span> HAKLO</div>
        <h2>Yeni şifren hazır olsun</h2>
        <p>Güçlü bir şifre seç ve güvenle devam et.</p>
        <div class="illus" aria-hidden="true"></div>
    </aside>

    <section class="card">
        <div class="head"><h1>Yeni şifre belirle</h1></div>

        @if ($errors->any())
            <div class="alert err">@foreach ($errors->all() as $err)<div>{{ $err }}</div>@endforeach</div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" novalidate>
            @csrf
            <input type="hidden" name="token" value="{{ request()->route('token') }}">
            <div>
                <label for="email">E-posta</label>
                <input id="email" name="email" type="email" class="input" value="{{ old('email', request('email')) }}" required autofocus>
            </div>

            <div class="field">
                <label for="password">Yeni şifre</label>
                <input id="password" name="password" type="password" class="input" required placeholder="••••••••">
                <button type="button" class="toggle-eye" onclick="const i=document.getElementById('password'); i.type=i.type==='password'?'text':'password'">Göster</button>
            </div>
            <div class="field">
                <label for="password_confirmation">Yeni şifre (tekrar)</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="input" required placeholder="••••••••">
            </div>

            <button class="btn" type="submit">Şifreyi güncelle</button>
        </form>
    </section>
</div>
</body>
</html>
