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
        .mbtn{ padding:2px 8px; border:1px solid #ddd; background:#f7f7f7; border-radius:6px; cursor:pointer; }
        .mremove{ margin-left:auto; border:none; background:#fff; font-size:18px; cursor:pointer; }
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
            <label><span>Ad Soyad</span><input type="text" placeholder="Adınız Soyadınız"></label>
            <label><span>E-posta</span><input type="email" placeholder="ornek@email.com"></label>
            <label><span>Telefon</span><input type="tel" placeholder="5xx xxx xx xx"></label>
            <label><span>Adres</span><textarea rows="3" placeholder="Açık adresiniz"></textarea></label>

            <h3>Ödeme Yöntemi</h3>
            <label class="radio"><input type="radio" name="pay" checked> Kredi/Banka Kartı</label>
            <label class="radio"><input type="radio" name="pay"> Kapıda Ödeme</label>

            <div class="card-grid">
                <label><span>Kart İsim</span><input type="text" placeholder="AD SOYAD"></label>
                <label><span>Kart No</span><input type="text" placeholder="1234 5678 9012 3456"></label>
                <label><span>Son Kullanma</span><input type="text" placeholder="AA/YY"></label>
                <label><span>CVC</span><input type="text" placeholder="123"></label>
            </div>

            <button class="btn btn-block" type="button" onclick="alert('Demo: ödeme entegrasyonu yok')">Siparişi Onayla</button>
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

<script>
    /* --- Helpers --- */
    function tl(n){ return Number(n||0).toFixed(2).replace('.',','); }
    function escapeHtml(s){ return String(s??'').replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;'); }

    /* --- Render summary from JSON (same shape as cart.show) --- */
    function renderCheckoutSummaryFromData(data){
        const itemsEl = document.getElementById('summaryItems');
        const emptyEl = document.getElementById('summaryEmpty');
        const footEl  = document.getElementById('summaryFoot');
        const subEl   = document.getElementById('sumSubtotal');
        const shipEl  = document.getElementById('sumShipping');
        const totEl   = document.getElementById('sumTotal');

        if(!data || !data.ok){
            emptyEl.hidden=false; itemsEl.innerHTML=''; footEl.hidden=true; return;
        }
        const items = data.items || [];
        if(items.length===0){
            emptyEl.hidden=false; itemsEl.innerHTML=''; footEl.hidden=true; return;
        }
        emptyEl.hidden = true;

        let subtotal = 0;
        itemsEl.innerHTML = items.map(it=>{
            const price = Number(it.price||0);
            const qty   = Number(it.qty||0);
            const line  = price * qty; subtotal += line;
            const img   = it.image
                ? `<img class="summary-thumb" src="${it.image}" alt="${escapeHtml(it.name)}">`
                : `<div class="summary-thumb"></div>`;
            return `<li class="summary-item" data-pid="${it.id}">
      ${img}
      <div class="summary-info">
        <div class="summary-name">${escapeHtml(it.name)}</div>
        <div class="summary-meta">
          <button type="button" class="mbtn" data-checkout-act="dec" data-pid="${it.id}" aria-label="Azalt">−</button>
          <span class="summary-qty">×${qty}</span>
          <button type="button" class="mbtn" data-checkout-act="inc" data-pid="${it.id}" aria-label="Arttır">+</button>
          <button type="button" class="mremove" data-checkout-act="remove" data-pid="${it.id}" aria-label="Kaldır">×</button>
        </div>
      </div>
      <div class="summary-price">
        <div>₺${tl(price)}</div>
        <div class="muted">× ${qty}</div>
        <div class="summary-total">₺${tl(line)}</div>
      </div>
    </li>`;
        }).join('');

        const freeLimit = 750.0;
        const shipping  = subtotal >= freeLimit || subtotal === 0 ? 0 : 49.90;

        subEl.textContent = '₺' + tl(subtotal);
        shipEl.innerHTML  = shipping===0 && subtotal>0 ? '<span class="free-badge">Ücretsiz</span>' : '₺'+tl(shipping);
        totEl.textContent = '₺' + tl(subtotal + shipping);

        footEl.hidden = false;
    }

    /* --- Fetch current summary from backend --- */
    async function fetchCheckoutSummary(){
        const r = await fetch("{{ route('cart.show') }}", { headers:{ "Accept":"application/json" }});
        const data = await r.json().catch(()=>null);
        renderCheckoutSummaryFromData(data);
    }

    /* --- Update cart from checkout (+/-/remove) --- */
    async function updateCartCheckout(productId, action){
        const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const r = await fetch("{{ route('cart.update') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": token
            },
            body: JSON.stringify({ product_id: productId, action: action }),
            credentials: "same-origin"
        });
        const raw = await r.text(); let res={}; try{ res=JSON.parse(raw); }catch(_){ res={ message:raw }; }
        console.log("CHECKOUT DEBUG -> update", {status:r.status, pid:productId, act:action, res});

        if (r.ok && res.ok){
            // Checkout özetini güncelle
            renderCheckoutSummaryFromData(res);
            // Varsa mini-cart'ı da güncelle (erkek sayfası ile senkron)
            if (typeof renderMiniCart === 'function') renderMiniCart(res);
        } else {
            alert(`Hata (${r.status}): ${res.message || raw}`);
        }
    }

    /* --- Event delegation for +/-/× in summary --- */
    document.addEventListener('click', (e)=>{
        const btn = e.target.closest('[data-checkout-act]');
        if(!btn) return;
        e.preventDefault();
        e.stopPropagation();
        const pid = Number(btn.getAttribute('data-pid'));
        const act = btn.getAttribute('data-checkout-act'); // inc | dec | remove
        if(!pid || !act) return;
        updateCartCheckout(pid, act);
    });

    /* --- Initial load --- */
    document.addEventListener('DOMContentLoaded', fetchCheckoutSummary);
</script>

</body>
</html>
