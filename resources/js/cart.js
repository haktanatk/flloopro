/* ===================== */
/* Mini Cart - cart.js   */
/* ===================== */

/** Mini-cart panelini aç/kapat */
window.toggleMiniCart = function () {
    const panel = document.getElementById('mini-cart-panel');
    const overlay = document.getElementById('mini-cart-overlay');
    if (!panel || !overlay) return;
    const hidden = panel.hasAttribute('hidden');
    hidden
        ? (panel.removeAttribute('hidden'), overlay.removeAttribute('hidden'))
        : (panel.setAttribute('hidden', true), overlay.setAttribute('hidden', true));
};

/** Küçük helper */
function csrf() {
    return $('meta[name="csrf-token"]').attr('content') || '';
}

/** Sepeti getir + alfabetik sırayla çiz (kayma olmasın) */
function refreshCart() {
    $.getJSON('/cart', function (data) {
        if (!data || !data.success) return;

        const items = (data.cart.items || []).slice(); // kopya
        const $ul   = $('#mini-cart-items-list');
        const $empty= $('#mini-cart-empty');
        const $count= $('#mini-cart-item-count');
        const $total= $('#mini-cart-total-price');

        $ul.empty();

        if (!items.length) {
            $empty.show();
            $count.text(0);
            $total.text('₺0.00');

            // checkout sayfası dinliyorsa bilgi ver
            try { window.dispatchEvent(new CustomEvent('cart:updated', { detail: data.cart })); } catch(_) {}
            return;
        }
        $empty.hide();

        // 🔹 alfabetik sırala (adı yoksa sku'ya göre)
        items.sort((a, b) => {
            const an = (a.product_name || a.sku || '').toString();
            const bn = (b.product_name || b.sku || '').toString();
            return an.localeCompare(bn, 'tr', { sensitivity: 'base' });
        });

        let totalCount = 0;

        items.forEach(it => {
            totalCount += Number(it.qty);
            const name = it.product_name || it.sku;

            // + / − butonlarını ekliyoruz (sadece AJAX ile çalışır)
            $ul.append(`
        <li class="mini-cart__item"
            style="display:flex;justify-content:space-between;gap:8px;margin-bottom:6px;align-items:center;">
          <div>
            <strong>${name}</strong>
            <div style="display:flex;gap:6px;align-items:center;margin-top:4px;">
              <button class="qty-btn" data-action="dec" data-id="${it.id}" data-sku="${it.sku}" aria-label="Azalt">−</button>
              <span class="qty-value" data-id="${it.id}" style="min-width:32px;text-align:center;display:inline-block;">${it.qty}</span>
              <button class="qty-btn" data-action="inc" data-id="${it.id}" data-sku="${it.sku}" aria-label="Arttır">+</button>
            </div>
          </div>
          <span>₺${Number(it.line_total).toFixed(2)}</span>
        </li>
      `);
        });

        $count.text(totalCount);
        $total.text('₺' + Number(data.cart.total).toFixed(2));

        // 👉 checkout sayfasına "güncel sepet"i yayınla
        try { window.dispatchEvent(new CustomEvent('cart:updated', { detail: data.cart })); } catch(_) {}
    });
}

/** refreshCart'ı global yap: checkout.blade bu fonksiyonu çağırabiliyor olacak */
window.refreshCart = refreshCart;

/** Ürün kartından "Sepete Ekle" */
$(document).on('click', '.add-to-cart', function () {
    const productId = $(this).data('id');
    const sku = $(this).data('sku');
    const qty = $('#adet-' + productId).val() || 1;

    $.ajax({
        url: '/cart',
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' },
        contentType: 'application/json; charset=UTF-8',
        data: JSON.stringify({ sku: String(sku), qty: Number(qty) }),
        success: function () { refreshCart(); }
    });
});

/** Mini-cart içindeki + / − (backend'e ek endpoint yazmadan) */
$(document).on('click', '.qty-btn', function () {
    const $btn   = $(this);
    const action = $btn.data('action');      // 'inc' | 'dec'
    const id     = $btn.data('id');          // basket_items.id
    const sku    = String($btn.data('sku')); // SKU
    const $val   = $(`.qty-value[data-id="${id}"]`);
    let current  = Number($val.text() || 1);

    if (action === 'inc') {
        // artır: sadece +1 POST et (store() qty'yi artırır)
        $.ajax({
            url: '/cart',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' },
            contentType: 'application/json; charset=UTF-8',
            data: JSON.stringify({ sku: sku, qty: 1 }),
            success: refreshCart
        });
        return;
    }

    // azalt
    const next = current - 1;

    if (next <= 0) {
        // komple sil
        $.ajax({
            url: `/cart/${id}`,
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' },
            success: refreshCart
        });
    } else {
        // hedef qty'ye set etmek için: sil → sonra next kadar ekle
        $.ajax({
            url: `/cart/${id}`,
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' },
            success: function () {
                $.ajax({
                    url: '/cart',
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' },
                    contentType: 'application/json; charset=UTF-8',
                    data: JSON.stringify({ sku: sku, qty: next }),
                    success: refreshCart
                });
            }
        });
    }
});

/** Sayfa açılışında mini-cart'ı getir */
$(function () { refreshCart(); });

document.addEventListener('DOMContentLoaded', () => {
    const slides = Array.from(document.querySelectorAll('.hero__slide'));
    const dots   = Array.from(document.querySelectorAll('#heroDots .dot'));
    if (!slides.length || !dots.length) return;

    let index = 0;
    const show = (i) => {
        slides.forEach((el, k) => el.classList.toggle('is-active', k === i));
        dots.forEach((el, k) => el.classList.toggle('is-active', k === i));
        index = i;
    };

    // Başlangıcı normalize et (tek bir is-active kalsın)
    show(0);

    const next = () => show((index + 1) % slides.length);

    // Otomatik geçiş
    let timer = setInterval(next, 4000);

    const resetTimer = () => {
        clearInterval(timer);
        timer = setInterval(next, 4000);
    };

    // Nokta tıklamaları
    dots.forEach((dot, i) => {
        dot.addEventListener('click', () => {
            show(i);
            resetTimer();
        });
    });

    // (İsteğe bağlı) Hero üzerinde hover olunca durdur
    const hero = document.getElementById('hero');
    if (hero) {
        hero.addEventListener('mouseenter', () => clearInterval(timer));
        hero.addEventListener('mouseleave', resetTimer);
    }
});

