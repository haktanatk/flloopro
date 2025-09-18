<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>HAKLO – Ürünler</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF, mini-cart için gerekli -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

@include('pages.menu');


<div class="mini-cart__overlay" hidden></div>


<section class="container hero" id="hero">
    <img class="hero__slide is-active" src="{{ asset('img/hero-1.png') }}" alt="Ücretsiz kargo!">
    <img class="hero__slide" src="{{ asset('img/hero-2.png') }}" alt="Yeni sezon">
    <img class="hero__slide" src="{{ asset('img/hero-3.png') }}" alt="Spor koleksiyonu">
    <img class="hero__slide" src="{{ asset('img/hero-4.png') }}" alt="Aksesuar trendleri">

    <div class="hero__text">
        <div class="kargo">Ücretsiz kargo, 30 gün iade süresi</div>
        <div class="alt">Kargo ücretsiz</div>
    </div>

    <div class="hero__controls" id="heroDots" aria-label="Slider noktaları">
        <button class="dot is-active" aria-label="1. görsel"></button>
        <button class="dot" aria-label="2. görsel"></button>
        <button class="dot" aria-label="3. görsel"></button>
        <button class="dot" aria-label="4. görsel"></button>
    </div>
</section>

<!-- ÜRÜN LİSTESİ -->
<main class="container">
    <h2 class="section-title">Ürünler</h2>
    <div class="grid">
        @forelse($products as $product)
            <div class="card">
                @php
                    $slug = \Illuminate\Support\Str::slug($product->name);
                    $img  = $product->image_url;
                    $src  = $img
                        ? (\Illuminate\Support\Str::startsWith($img, ['http://','https://','/']) ? $img : asset('storage/'.$img))
                        : asset('img/placeholder-1.jpg');
                @endphp

                <a href="{{ route('product.show', ['product' => $product->id, 'slug' => $slug]) }}">
                    <img src="{{ $src }}" alt="{{ $product->name }}">
                </a>

                <h3>
                    <a href="{{ route('product.show', ['product' => $product->id, 'slug' => $slug]) }}">
                        {{ $product->name }}
                    </a>
                </h3>

                <div class="price">
                    ₺{{ number_format($product->price ?? 0, 2, ',', '.') }}
                </div>
                <div class="quantity-control">
                    <input type="number" id="adet-{{ $product->id }}" value="1" min="1"
                           style="width:50px; text-align:center;">
                    <button
                        type="button"
                        class="btn add-to-cart"
                        data-sku="{{ $product->sku }}"
                        data-id="{{ $product->id }}">
                        Sepete Ekle
                    </button>
                </div>
            </div>
        @empty
            <p>Bu kategoride henüz ürün yok.</p>
        @endforelse
    </div>
</main>

<footer class="container footer">
    <div>Hacoder 2025</div>
</footer>

