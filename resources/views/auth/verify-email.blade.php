<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>E-posta Doğrulama • HAKLO</title>
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
        <h2>Hesabını doğrula</h2>
        <p>Güvenli alışveriş için e-posta adresine gönderdiğimiz bağlantıya tıkla.</p>
        <div class="illus" aria-hidden="true"></div>
    </aside>

    <section class="card">
        <div class="head"><h1>E-posta doğrulaması</h1></div>

        @if (session('status') == 'verification-link-sent')
            <div class="alert ok" style="background:#ecfdf5;border-color:#99f6e4;color:#0f766e;">
                Doğrulama linki e-postana tekrar gönderildi.
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button class="btn" type="submit">Doğrulama e-postası gönder</button>
        </form>

        <div class="brand-mini" style="margin-top:10px;">
            <span class="h">H</span><span>Mail gelmediyse spam klasörünü de kontrol et.</span>
        </div>

        <form method="POST" ac
