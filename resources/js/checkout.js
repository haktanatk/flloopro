// Basit yardımcılar
function isEmail(v){ return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(v||'').trim()); }
function isPhone(v){ return /^[0-9\s()+-]{10,}$/.test(String(v||'').trim()); }

function getCustomerFromForm(){
    return {
        full_name: $('#full_name').val()?.trim(),
        email:     $('#email').val()?.trim(),
        phone:     $('#phone').val()?.trim(),
        address:   $('#address').val()?.trim(),
    };
}

function validateCustomer(c){
    const errors = {};
    if(!c.full_name || c.full_name.length < 3) errors.full_name = 'Ad Soyad en az 3 karakter olmalı';
    if(!c.email || !isEmail(c.email))          errors.email     = 'Geçerli bir e-posta giriniz';
    if(!c.phone || !isPhone(c.phone))          errors.phone     = 'Geçerli bir telefon giriniz';
    if(!c.address || c.address.length < 10)    errors.address   = 'Adres en az 10 karakter olmalı';
    return errors;
}

// Inline hata göstermek istersen çok basit bir alert kullanıyoruz (istersen geliştiririz)
function showErrors(errors){
    const msgs = Object.values(errors);
    if(msgs.length){ alert('Lütfen formu kontrol edin:\n- ' + msgs.join('\n- ')); }
}

// Çift bağları temizle → tek handler
$(document).off('click', '#btn-order-confirm').on('click', '#btn-order-confirm', function () {
    const $btn = $(this);

    // double-click koruması
    if ($btn.data('busy')) return;
    $btn.data('busy', true).prop('disabled', true).text('İşleniyor...');

    const customer = getCustomerFromForm();
    const errs = validateCustomer(customer);
    if (Object.keys(errs).length) {
        showErrors(errs);
        $btn.prop('disabled', false).text('Siparişi Onayla').data('busy', false);
        return;
    }

    $.ajax({
        url: '/checkout/confirm',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        data: JSON.stringify({ customer }),

        success: function (res, _status, xhr) {
            // 201'de Location header geldiyse direkt oraya git
            const loc = xhr.getResponseHeader('Location');
            if (loc) { window.location = loc; return; }

            // Header yoksa JSON fallback
            if (res && res.success && res.order_number) {
                window.location = '/tesekkur?order=' + encodeURIComponent(res.order_number);
                return;
            }

            alert('Sipariş oluşturuldu ama yönlendirme bilgisi gelmedi.');
            $btn.prop('disabled', false).text('Siparişi Onayla').data('busy', false);
        },

        error: function (xhr) {
            const res = xhr.responseJSON || {};
            alert('Hata: ' + (res.message || 'Bilinmeyen hata'));
            $btn.prop('disabled', false).text('Siparişi Onayla').data('busy', false);
        }
    });
});

