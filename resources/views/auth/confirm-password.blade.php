<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>Şifreyi Doğrula • HAKLO</title>
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
        <h2>Güvenlik adımı</h2>
        <p>Devam etmek için lütfen şifreni tekrar gir.</p>
        <div class="illus" aria-hidden="true"></div>
    </aside>

    <section class="card">
        <div class="head"><h1>Şifreyi doğrula</h1></div>

        @if ($errors->any())
            <div class="alert err">@foreach ($errors->all() as $err)<div>{{ $err }}</div>@endforeach</div>
        @endif

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf
            <div class="field">
                <label for="password">Şifre</label>
                <input id="password" name="password" type="password" class="input" required autocomplete="current-password" placeholder="••••••••">
                <button type="button" class="toggle-eye" onclick="const i=document.getElementById('password'); i.type=i.type==='password'?'text':'password'">Göster</button>
            </div>
            <button class="btn" type="submit">Devam et</button>
        </form>
    </section>
</div>
</body>
</html>
