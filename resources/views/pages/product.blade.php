<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>{{ $product->name }} – HAKLO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>

<header class="site-header">
    <div class="container nav">
        <a class="brand" href="{{ route('home') }}"><span class="hlogo">H</span> HAKLO</a>

        <nav class="menu">
            <div class="menu-item"><a href="/kadin" class="link">Kadın</a></div>
            <div class="menu-item"><a href="/erkek" class="link">Erkek</a></div>
            <div class="menu-item"><a href="/cocuk" class="link">Çocuk</a></div>
        </nav>

        <form class="search" role="search" onsubmit="event.preventDefault();">
            <input type="search" placeholder="Ara" aria-label="Ara">
        </form>

        <div class="mini-cart" id="miniCart">
            <button class="mini-cart__button" type="button" aria-expanded="false">
                🛒 Sepet <span class="mini-cart__count">0</span>
            </button>

            <!-- PANEL: başlığa kapat butonu eklendi -->
            <div class="mini-cart__panel" hidden>
                <div class="mini-cart__head">
                    Sepetiniz
                    <button type="button" class="mini-cart__close" aria-label="Kapat">×</button>
                </div>

                <ul class="mini-cart__items"></ul>
                <div class="mini-cart__empty">Sepetiniz boş.</div>

                <div class="mini-cart__footer">
                    <div class="mini-cart__total"></div>
                    <a class="btn btn-block" href="{{ route('checkout') }}">Siparişi Tamamla</a>
                </div>
            </div>
        </div>
    </div>
</header>
<div class="mini-cart__overlay" hidden></div>

@php
    $img = $product->image_url;
    $src = $img
        ? (\Illuminate\Support\Str::startsWith($img, ['http://','https://','/']) ? $img : asset('storage/'.$img))
        : asset('img/placeholder-1.jpg');
@endphp

<main class="container" style="margin-top:28px;">
    <div class="pdp">
        <div class="pdp__media">
            <img class="pdp__img" src="{{ $src }}" alt="{{ $product->name }}">
        </div>

        <div class="pdp__info">
            <h1 class="pdp__title">{{ $product->name }}</h1>
            <div class="pdp__price">₺{{ number_format($product->price ?? 0, 2, ',', '.') }}</div>
            <div class="pdp__sku">Ürün Kodu: {{ $product->sku }}</div>

            <div class="pdp__buy">
                <label class="pdp__qty">
                    Adet:
                    <input type="number" id="adet-{{ $product->id }}" value="1" min="1">
                </label>
                <button type="button" class="btn"
                        onclick="addBasket('{{ $product->sku }}', document.getElementById('adet-{{ $product->id }}').value)">
                    Sepete Ekle
                </button>
            </div>

            <div class="pdp__desc">{!! nl2br(e($product->long_desc ?? '.')) !!}</div>
        </div>
    </div>

    <!-- ÖNERİLEN ÜRÜNLER -->
    <hr style="margin:36px 0; border:none; border-top:1px solid #eee;">
    <section class="container" style="margin-top:8px;">
        <h2 class="section-title">Önerilen Ürünler</h2>
        <div class="grid">
            @forelse($recommended as $rec)
                @php
                    $slug = \Illuminate\Support\Str::slug($rec->name);
                    $img  = $rec->image_url;
                    $src  = $img
                        ? (\Illuminate\Support\Str::startsWith($img, ['http://','https://','/']) ? $img : asset('storage/'.$img))
                        : asset('img/placeholder-1.jpg');
                @endphp

                <div class="card">
                    <a href="{{ route('product.show', ['product' => $rec->id, 'slug' => $slug]) }}">
                        <img src="{{ $src }}" alt="{{ $rec->name }}">
                    </a>
                    <h3 style="margin:8px 0;">
                        <a href="{{ route('product.show', ['product' => $rec->id, 'slug' => $slug]) }}">
                            {{ $rec->name }}
                        </a>
                    </h3>
                    <div class="price">₺{{ number_format($rec->price ?? 0, 2, ',', '.') }}</div>
                    <div class="quantity-control" style="margin-top:8px;">
                        <input type="number" id="oneri-adet-{{ $rec->id }}" value="1" min="1"
                               style="width:56px; text-align:center;">
                        <button onclick="addBasket(sku, qty)">Tıkla
                        Sepete Ekle
                        </button>
                    </div>
                </div>
            @empty
                <p>Şu an öneri bulunamadı.</p>
            @endforelse
        </div>
    </section>
</main>
</body>
</html>



<script>

    $("addBasket(sku ,qty)").on("click", function() {
         $.ajax({
        url:'cart-url',
    type: 'POST',
    data: {
        sku:'sku',
        qty: 'qty',
        _token: "{{ csrf_token() }}"
    },
        success: function(response){
        console.log("Başarılı:", response);
        },
        error: function(xhr){
        consolne.log("Hata", xhr)
        }
        });
    });
<script>


   function renderMiniCart(data){
        const root=document.getElementById('miniCart'); if(!root) return;
        const c=root.querySelector('.mini-cart__count'), ul=root.querySelector('.mini-cart__items'),
            empty=root.querySelector('.mini-cart__empty'), total=root.querySelector('.mini-cart__total');
        if (typeof data.count==='number' && c) c.textContent=data.count;
        if (Array.isArray(data.items) && ul) {
            ul.innerHTML = data.items.map(it=>{
                const price=formatTL(it.price), line=formatTL(it.line_total);
                const img=it.image?`<img class="mini-cart__img" src="${it.image}" alt="${escapeHtml(it.name)}">`:'';
                return `<li class="mini-cart__item" data-pid="${it.id}">
        <div class="mini-cart__media">${img}</div>
        <div class="mini-cart__info">
          <div class="mini-cart__name">${escapeHtml(it.name)}</div>
          <div class="mini-cart__meta"><span class="mini-cart__price">₺${price}</span><span class="mini-cart__line-total">₺${line}</span></div>
          <div class="mini-cart__controls">
            <button type="button" class="mbtn" data-act="dec">−</button>
            <span class="mini-cart__qty">×${it.qty}</span>
            <button type="button" class="mbtn" data-act="inc">+</button>
            <button type="button" class="mremove" data-act="remove" aria-label="Kaldır">×</button>
          </div>
        </div>
      </li>`;
            }).join('');
        }
        if (empty) empty.style.display = (data.items && data.items.length) ? 'none' : '';
        if (total && typeof data.total==='number') total.textContent = 'Toplam: ₺' + formatTL(data.total);
    }

    const cartRoot=document.getElementById('miniCart');
    const panelEl=cartRoot?.querySelector('.mini-cart__panel');
    const overlay=document.querySelector('.mini-cart__overlay');

    function openMiniCart(){ if(panelEl?.hasAttribute('hidden')) panelEl.removeAttribute('hidden'); if(overlay?.hasAttribute('hidden')) overlay.removeAttribute('hidden'); cartRoot?.querySelector('.mini-cart__button')?.setAttribute('aria-expanded','true'); }
    function closeMiniCart(){ panelEl?.setAttribute('hidden',''); overlay?.setAttribute('hidden',''); cartRoot?.querySelector('.mini-cart__button')?.setAttribute('aria-expanded','false'); }

    ['click','mousedown','mouseup'].forEach(evt=>{
        panelEl?.addEventListener(evt,(e)=>{ e.stopPropagation(); e.stopImmediatePropagation(); }, true);
    });

    cartRoot.addEventListener('click',(e)=>{
        const openBtn=e.target.closest('.mini-cart__button'); if(openBtn){ e.preventDefault(); openMiniCart(); return; }
        const closeBtn=e.target.closest('.mini-cart__close'); if(closeBtn){ e.preventDefault(); closeMiniCart(); return; }
        const actBtn=e.target.closest('[data-act]'); if(actBtn){
            e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
            const li=actBtn.closest('.mini-cart__item'); if(!li) return;
            const pid=Number(li.getAttribute('data-pid')); const act=actBtn.getAttribute('data-act');
            if(!pid || !act) return; updateCart(pid, act); return;
        }
        const go=e.target.closest('.mini-cart__footer a[href]'); if(go){ e.preventDefault(); closeMiniCart(); window.location.assign(go.getAttribute('href')); }
    });

    overlay.addEventListener('click',()=> closeMiniCart());
    document.addEventListener('keydown',(e)=>{ if(e.key==='Escape') closeMiniCart(); });

    async function updateCart(productId, action){
        const r=await fetch("{{ route('cart.update') }}",{
            method:"POST",
            headers:{ "Content-Type":"application/json","Accept":"application/json","X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ product_id: productId, action }), credentials:"same-origin"
        });
        const raw=await r.text(); let res={}; try{ res=JSON.parse(raw); }catch(_){ res={message:raw}; }
        if(r.ok && res.ok) renderMiniCart(res); else alert(`Hata (${r.status}): ${res.message || raw}`);
    }

    function escapeHtml(s){return String(s??'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');}
    function formatTL(n){const v=Number(n||0);return v.toFixed(2).replace('.',',');}

    window.addEventListener('load', async()=>{
        const r=await fetch("{{ route('cart.show') }}",{ headers:{ "Accept":"application/json" }});
        const data=await r.json().catch(()=>null); if(data && data.ok) renderMiniCart(data);
    }); */
</script>

<style>
    .pdp{ display:grid; grid-template-columns: 1fr 1fr; gap:28px; }
    .pdp__img{ width:100%; max-height:700px; object-fit:cover; border-radius:12px; border:1px solid #eee; background:#fafafa; }
    .pdp__title{ font-size:1.6rem; margin-bottom:4px; }
    .pdp__price{ font-size:1.3rem; font-weight:700; margin:8px 0; }
    .pdp__sku{ color:#666; font-size:.95rem; }
    .pdp__buy{ display:flex; align-items:center; gap:12px; margin:14px 0 18px; }
    .pdp__qty input{ width:72px; text-align:center; padding:6px 8px; border-radius:8px; border:1px solid #ddd; }
    .pdp__desc{ margin-top:8px; color:#333; line-height:1.5; }
    @media (max-width: 900px){ .pdp{ grid-template-columns: 1fr; } }

    .mini-cart{ position:relative; }
    .mini-cart__panel{ position:absolute; right:0; top:52px; width:360px; max-width:90vw; background:#fff; border:1px solid #e8e8e8; border-radius:16px; box-shadow:0 18px 40px rgba(0,0,0,.12); padding:12px; z-index:1001; }
    .mini-cart__head{ display:flex; align-items:center; justify-content:space-between; font-weight:700; margin-bottom:6px; }
    .mini-cart__close{ background:none; border:none; font-size:22px; line-height:1; cursor:pointer; color:#555; }
    .mini-cart__items{ list-style:none; margin:6px 0; padding:0; max-height:320px; overflow:auto; }
    .mini-cart__item{ display:flex; gap:10px; padding:10px 0; border-bottom:1px solid #f1f1f1; }
    .mini-cart__media{ flex:0 0 auto; }
    .mini-cart__img{ width:56px; height:56px; object-fit:cover; border-radius:8px; display:block; }
    .mini-cart__info{ flex:1; }
    .mini-cart__meta{ display:flex; gap:10px; font-size:.9rem; color:#444; }
    .mini-cart__controls{ display:flex; align-items:center; gap:8px; margin-top:6px; }
    .mbtn{ padding:2px 8px; border:1px solid #ddd; background:#f7f7f7; border-radius:6px; cursor:pointer; }
    .mremove{ margin-left:auto; border:none; background:#fff; font-size:18px; cursor:pointer; }
    .mini-cart__footer{ margin-top:8px; }
    .mini-cart__total{ font-weight:700; margin:8px 0; }
    .mini-cart__button{ display:inline-flex; align-items:center; gap:8px; padding:.5rem .8rem; border-radius:999px; background:#1f6feb; color:#fff; border:none; cursor:pointer; }
    .mini-cart__count{ background:#fff; color:#1f6feb; border-radius:999px; padding:0 .45rem; font-weight:700; }
    .mini-cart__overlay{ position:fixed; inset:0; background:rgba(0,0,0,.06); z-index:1000; }
</style>
</body>
</html>



