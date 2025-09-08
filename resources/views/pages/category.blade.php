<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Kategori' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>
<header class="site-header">
    <div class="container nav">
        <a class="brand" href="{{ route('home') }}"><span class="hlogo">H</span> HAKLO</a>
    </div>
</header>

<main class="container" style="margin-top:20px;">
    <h2 class="section-title">{{ $title ?? 'Kategori' }}</h2>

    {{-- Placeholder grid (statik) --}}
    <div class="grid">
        <div class="card">
            <img src="{{ asset('img/placeholder-1.jpg') }}" alt="Ürün">
            <h3>Ürün Adı</h3>
            <div class="price">₺000,00</div>
            <button class="btn" type="button">Sepete Ekle</button>
        </div>
        <div class="card">
            <img src="{{ asset('img/placeholder-2.jpg') }}" alt="Ürün">
            <h3>Ürün Adı</h3>
            <div class="price">₺000,00</div>
            <button class="btn" type="button">Sepete Ekle</button>
        </div>
        <div class="card">
            <img src="{{ asset('img/placeholder-3.jpg') }}" alt="Ürün">
            <h3>Ürün Adı</h3>
            <div class="price">₺000,00</div>
            <button class="btn" type="button">Sepete Ekle</button>
        </div>
        <div class="card">
            <img src="{{ asset('img/placeholder-4.jpg') }}" alt="Ürün">
            <h3>Ürün Adı</h3>
            <div class="price">₺000,00</div>
            <button class="btn" type="button">Sepete Ekle</button>
        </div>
    </div>

    <p style="color:#6b7280;margin-top:10px">
        * Bu sayfa mockup’tır. Ürünler SQL + Go servisiyle sonradan dinamik listelenecek.
    </p>
</main>
</body>
</html>
