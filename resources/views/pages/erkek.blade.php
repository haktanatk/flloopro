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
    <h2 class="section-title">Erkek Ürünleri</h2>

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

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $(document).on("click", ".add-to-cart", function () {
        var qty = document.getElementById("adet-{{ $product->id }}")
        var productId = $(this).data("id");
        var sku = $(this).data("sku");
        var qty = $("#adet-" + productId).val();
        $.ajax({
            url: '/cart',
            type: 'POST',
            contentType: 'application/json; charset=UTF-8',
            data: JSON.stringify({
                sku: sku,
                qty: Number(qty),
                _token: $('meta[name="csrf-token"]').attr('content')
            }),
            success: function(response){
                console.log(response);
            },
            error: function(xhr){
                console.log("Hata", xhr)
            }
        });
    });
</script>



