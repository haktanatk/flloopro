<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>Şifremi Unuttum • HAKLO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>@import url('');/* same theme */</style>
</head>
<body class="hak-login">
<style>
    /* same CSS as register (kısalık için tekrar etmiyorum) */
</style>
<div class="shell">
    <aside class="side">
        <div class="logo"><span class="h">H</span> HAKLO</div>
        <h2>Şifreni mi unuttun?</h2>
        <p>E-posta adresine şifre sıfırlama linki gönderelim.</p>
        <div class="illus" aria-hidden="true"></div>
    </aside>

    <section class="card">
        <div class="head">
            <h1>Şifre sıfırlama</h1>
            <div class="sub">Hesabına giriş yapmayı kolaylaştıralım.</div>
        </div>

        @if (session('status'))
            <div class="alert ok" style="background:#ecfdf5;border-color:#99f6e4;color:#0f766e;">
                {{ session('status') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert err">@foreach ($errors->all() as $err)<div>{{ $err }}</div>@endforeach</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" novalidate>
            @csrf
            <div>
                <label for="email">E-posta</label>
                <input id="email" name="email" type="email" class="input" value="{{ old('email') }}" required autofocus placeholder="ornek@haklo.com">
            </div>
            <button class="btn" type="submit">Sıfırlama bağlantısı gönder</button>
            <div class="brand-mini"><span class="h">H</span><span>Mail birkaç dakika içinde gelir</span></div>
        </form>
    </section>
</div>
</body>
</html>
