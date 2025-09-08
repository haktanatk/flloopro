<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>HAKLO – Ürünler</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

@include('pages.menu');

<main class="container" style="margin-top:30px;">
    <h2 class="section-title">Kadın Ürünleri</h2>

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
</body>
</html>
<script src = "https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>

    $(document).on("click", ".add-to-cart", function () {
        var qty = document.getElementById("adet-{{ $product->id }}")
        var productId = $(this).data("id");
        var sku = $(this).data("sku");
        var qty = $("#adet-" + productId).val();
        $.ajax({
            url:'/cart',
            type: 'POST',
            data: {
                sku:"{{$product->sku}}",
                qty: qty,
                _token: "{{ csrf_token() }}"
            },
            success: function(response){
                alert(response['message']);
            },
            error: function(xhr){
                console.log("Hata", xhr)
            }
        });
    });




</script>
<style>
    /* Ürünler yan yana (responsive grid) */
    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 24px;
    }

    .card {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 16px;
        padding: 14px;
        box-shadow: 0 10px 24px rgba(0, 0, 0, .04);
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .card img {
        width: 100%;
        height: 260px;
        object-fit: cover;
        border-radius: 12px;
    }

    .quantity-control {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Mini-cart görünüm ve küçük görsel */
    .mini-cart {
        position: relative;
    }

    .mini-cart__panel {
        position: absolute;
        right: 0;
        top: 52px;
        width: 360px;
        max-width: 90vw;
        background: #fff;
        border: 1px solid #e8e8e8;
        border-radius: 16px;
        box-shadow: 0 18px 40px rgba(0, 0, 0, .12);
        padding: 12px;
        z-index: 1001;
    }

    .mini-cart__head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .mini-cart__close {
        background: none;
        border: none;
        font-size: 22px;
        line-height: 1;
        cursor: pointer;
        color: #555;
    }

    .mini-cart__items {
        list-style: none;
        margin: 6px 0;
        padding: 0;
        max-height: 320px;
        overflow: auto;
    }

    .mini-cart__item {
        display: flex;
        gap: 10px;
        padding: 10px 0;
        border-bottom: 1px solid #f1f1f1;
    }

    .mini-cart__media {
        flex: 0 0 auto;
    }

    .mini-cart__img {
        width: 56px;
        height: 56px;
        object-fit: cover;
        border-radius: 8px;
        display: block;
    }

    .mini-cart__info {
        flex: 1;
    }

    .mini-cart__meta {
        display: flex;
        gap: 10px;
        font-size: .9rem;
        color: #444;
    }

    .mini-cart__controls {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 6px;
    }

    .mbtn {
        padding: 2px 8px;
        border: 1px solid #ddd;
        background: #f7f7f7;
        border-radius: 6px;
        cursor: pointer;
    }

    .mremove {
        margin-left: auto;
        border: none;
        background: #fff;
        font-size: 18px;
        cursor: pointer;
    }

    /* Overlay: panel açıkken arka planı kapla, dışarı tıklama için */
    .mini-cart__overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .06);
        z-index: 1000;
    }

    .flash {
        position: fixed;
        left: 50%;
        top: 20px;
        transform: translateX(-50%);
        padding: .6rem 1rem;
        border-radius: .5rem;
        background: #111;
        color: #fff;
        box-shadow: 0 6px 18px rgba(0, 0, 0, .2);
        font-size: .95rem;
        z-index: 9999;
    }
</style>
