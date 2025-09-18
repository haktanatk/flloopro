<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>HAKLO – Siparişi Tamamla</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css','resources/js/app.js'])

    <style>
        /* --- Sayfa düzeni (solda form, sağda özet) --- */
        .checkout{
            display:grid;
            grid-template-columns: 1fr 420px;
            gap:28px;
        }
        @media (max-width: 900px){
            .checkout{ grid-template-columns: 1fr; }
        }
        .checkout__form, .checkout__summary{
            background:#fff; border:1px solid #eee; border-radius:16px; padding:16px;
            box-shadow:0 10px 24px rgba(0,0,0,.04);
        }

        /* --- Sipariş özeti stilleri --- */
        .summary-items{ list-style:none; margin:0; padding:0; }
        .summary-item{ display:flex; gap:12px; padding:10px 0; border-bottom:1px solid #f2f2f2; }
        .summary-thumb{ width:64px; height:64px; object-fit:cover; border-radius:8px; background:#fafafa; border:1px solid #eee; }
        .summary-info{ flex:1; display:flex; flex-direction:column; gap:6px; }
        .summary-name{ font-weight:600; }
        .summary-meta{ display:flex; align-items:center; gap:8px; }
        .summary-price{ text-align:right; min-width:110px; }
        .summary-total{ font-weight:700; }
        .summary-foot .row{ display:flex; justify-content:space-between; margin-top:6px; }
        .muted{ color:#666; }
        .free-badge{ font-size:.85rem; padding:2px 6px; border-radius:999px; background:#e9f7ef; color:#1e7e34; }
        .empty-box{ padding:16px; background:#fafafa; border:1px dashed #ddd; border-radius:12px; color:#666; }
        .btn.btn-block{ display:block; width:100%; }
        .link-under{ display:inline-block; margin-top:6px; font-size:.95rem; }
        .card-grid{ display:grid; grid-template-columns: 1fr 1fr; gap:12px; }
        @media (max-width: 600px){ .card-grid{ grid-template-columns:1fr; } }
        .checkout__form label{ display:flex; flex-direction:column; gap:6px; margin-bottom:10px; }
        .checkout__form input, .checkout__form textarea{
            padding:.55rem .7rem; border:1px solid #ddd; border-radius:10px;
        }
        .radio{ display:flex; align-items:center; gap:8px; margin-bottom:6px; }
    </style>
</head>
<body>

<header class="site-header">
    <div class="container nav">
        <a class="brand" href="{{ route('home') }}"><span class="hlogo">H</span> HAKLO</a>
    </div>
</header>

<section class="container" style="margin-top:20px;">
    <h2 class="section-title">Siparişi Tamamla</h2>

    <div class="checkout">
        <!-- Sol: Form -->
        <form class="checkout__form" onsubmit="return false;">
            <h3>Teslimat Bilgileri</h3>

            <label><span>Ad Soyad</span>
                <input id="full_name" name="customer[full_name]" type="text" placeholder="Adınız Soyadınız">
            </label>

            <label><span>E-posta</span>
                <input id="email" name="customer[email]" type="email" placeholder="ornek@email.com">
            </label>

            <label><span>Telefon</span>
                <input id="phone" name="customer[phone]" type="tel" placeholder="5xx xxx xx xx">
            </label>

            <label><span>Adres</span>
                <textarea id="address" name="customer[address]" rows="3" placeholder="Açık adresiniz"></textarea>
            </label>

            <div class="card-grid">
                <label><span>Kart İsim</span><input type="text" placeholder="AD SOYAD"></label>
                <label><span>Kart No</span><input type="text" placeholder="1234 5678 9012 3456"></label>
                <label><span>Son Kullanma</span><input type="text" placeholder="AA/YY"></label>
                <label><span>CVC</span><input type="text" placeholder="123"></label>
            </div>

            <button id="btn-order-confirm" class="btn btn-block" type="button">Siparişi Onayla</button>
            <a href="{{ route('shop') }}" class="link-under">← Alışverişe dön</a>
        </form>

        <!-- Sağ: Sipariş Özeti -->
        <aside class="checkout__summary">
            <h3>Sipariş Özeti</h3>

            <ul id="summaryItems" class="summary-items"></ul>
            <div id="summaryEmpty" class="empty-box" hidden>Sepetiniz boş.</div>

            <div id="summaryFoot" class="summary-foot" hidden>
                <div class="row">
                    <span class="muted">Ara Toplam</span>
                    <span id="sumSubtotal"></span>
                </div>
                <div class="row">
                    <span class="muted">Kargo</span>
                    <span id="sumShipping"></span>
                </div>
                <div class="row summary-total" style="margin-top:6px;">
                    <span>Genel Toplam</span>
                    <span id="sumTotal"></span>
                </div>
            </div>
        </aside>
    </div> <!-- /.checkout -->
</section> <!-- /.container -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).on('click', '#btn-order-confirm', async function () {
        const $btn = $(this).prop('disabled', true).text('İşleniyor...');

        const customer = {
            full_name: $('#full_name').val()?.trim(),
            email:     $('#email').val()?.trim(),
            phone:     $('#phone').val()?.trim(),
            address:   $('#address').val()?.trim(),
        };

        try {
            const res = await fetch('/checkout/confirm', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ customer })
            });

            if (res.status === 201) {
                const loc = res.headers.get('Location');
                if (loc) { window.location = loc; return; }
                const data = await res.json().catch(()=>null);
                if (data?.order_number) {
                    window.location = '/tesekkur?order=' + encodeURIComponent(data.order_number);
                    return;
                }
            }

            const err = await res.json().catch(()=>({message:'İşlem tamamlanamadı'}));
            alert('Hata: ' + (err.message || 'Bilinmeyen hata'));
        } catch (e) {
            alert('Ağ hatası');
        } finally {
            $btn.prop('disabled', false).text('Siparişi Onayla');
        }
    });
</script>

<script type="module">
    const tl = n => '₺' + Number(n||0).toFixed(2);

    async function loadCheckout() {
        try {
            const r = await fetch('/cart', { headers: { 'Accept': 'application/json' }});
            const res = await r.json();
            renderCheckout(res?.success ? res.cart : null);
        } catch (e) {
            console.error(e);
            renderCheckout(null);
        }
    }

    function renderCheckout(cart) {
        const itemsEl = document.getElementById('summaryItems');
        const emptyEl = document.getElementById('summaryEmpty');
        const footEl  = document.getElementById('summaryFoot');
        const subEl   = document.getElementById('sumSubtotal');
        const shipEl  = document.getElementById('sumShipping');
        const totEl   = document.getElementById('sumTotal');

        if (!cart || !Array.isArray(cart.items) || cart.items.length === 0) {
            emptyEl.hidden = false;
            itemsEl.innerHTML = '';
            footEl.hidden = true;
            return;
        }

        emptyEl.hidden = true;

        const items = cart.items.slice().sort((a,b)=>{
            const an = (a.product_name || a.sku || '').toString();
            const bn = (b.product_name || b.sku || '').toString();
            return an.localeCompare(bn, 'tr', { sensitivity:'base' });
        });

        let subtotal = 0;

        itemsEl.innerHTML = items.map(it => {
            const name  = it.product_name || it.sku;
            const qty   = Number(it.qty||0);
            const price = Number((it.unit_price ?? it.price) || 0);
            const line  = Number(it.line_total ?? (price * qty));
            subtotal += line;

            // shop.blade mantığı: eğer tam URL değilse /storage/ öneki ekle
            const imgPath = it.image_url || '';
            const imgFull = imgPath.startsWith('http') || imgPath.startsWith('/storage/')
                ? imgPath
                : '/storage/' + imgPath.replace(/^\/+/, '');

            const img = imgPath
                ? `<img class="summary-thumb" src="${imgFull}" alt="${name}">`
                : `<div class="summary-thumb"></div>`;

            return `
            <li class="summary-item">
              ${img}
              <div class="summary-info">
                <div class="summary-name">${name}</div>
                <div class="summary-meta"><span class="muted">× ${qty}</span></div>
              </div>
              <div class="summary-price">
                <div>${tl(price)}</div>
                <div class="muted">× ${qty}</div>
                <div class="summary-total">${tl(line)}</div>
              </div>
            </li>`;
        }).join('');

        const freeLimit = 750.0;
        const shipping  = subtotal >= freeLimit || subtotal === 0 ? 0 : 49.90;

        subEl.textContent = tl(subtotal);
        shipEl.innerHTML  = shipping === 0 && subtotal > 0
            ? '<span class="free-badge">Ücretsiz</span>'
            : tl(shipping);
        totEl.textContent = tl(subtotal + shipping);
        footEl.hidden = false;
    }

    document.addEventListener('DOMContentLoaded', loadCheckout);
</script>
<button id="btn-stock-check">Stok Kontrolü</button>
<ul id="stock-results"></ul>

<script>
    $('#btn-stock-check').on('click', function () {
        $.ajax({
            url: '/checkout/review',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (res) {
                const $ul = $('#stock-results').empty();
                if (!res || !res.success) { $ul.append('<li>Hata</li>'); return; }

                if (res.results.length === 0) { $ul.append('<li>Sepet boş</li>'); return; }

                res.results.forEach(r => {
                    const line = `${r.sku} → ${r.ok ? 'OK' : ('HATA: ' + r.message)} (İstenen: ${r.requested})`;
                    $ul.append('<li>'+ line +'</li>');
                });

                if (res.all_ok) {
                    $ul.append('<li><strong>Tüm ürünler uygun. Siparişi onaylayabilirsin.</strong></li>');
                }
            }
        });
    });
</script>






</body>
</html>
