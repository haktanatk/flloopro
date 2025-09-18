<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>Teşekkürler</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        body{ background:#f9fafb; font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto; }
        .card{ max-width:720px; margin:60px auto; background:#fff; border-radius:16px; padding:24px;
            box-shadow:0 8px 24px rgba(0,0,0,.06); }
        h2{ margin:0 0 8px; color:#16a34a; }
        .muted{ color:#6b7280; }
        .lines{ margin-top:18px; border-top:1px solid #eee; }
        .line{ display:flex; gap:12px; padding:10px 0; border-bottom:1px solid #f3f4f6; }
        .line .name{ flex:1; font-weight:600; }
        .line .qty{ width:64px; text-align:center; }
        .line .price{ width:140px; text-align:right; }
        .total{ display:flex; justify-content:flex-end; gap:16px; margin-top:12px; font-weight:700; }
        .btn{ display:inline-block; background:#2563eb; color:#fff; text-decoration:none; padding:10px 16px;
            border-radius:10px; font-weight:600; }
        .btn:hover{ background:#1d4ed8; }
    </style>
</head>
<body>
<div class="card">
    <h2>Teşekkürler! 🎉</h2>
    <p class="muted">Siparişiniz başarıyla oluşturuldu.</p>

    <p><strong>Sipariş No:</strong> {{ $order->order_number }}</p>

    @if(!empty($order->customer_name) || !empty($order->customer_address))
        <div style="margin:10px 0 4px; color:#374151;">
            <div><strong>Alıcı:</strong> {{ $order->customer_name ?? '-' }}</div>
            <div><strong>Telefon:</strong> {{ $order->customer_phone ?? '-' }}</div>
            <div><strong>E-posta:</strong> {{ $order->customer_email ?? '-' }}</div>
            <div><strong>Adres:</strong> {{ $order->customer_address ?? '-' }}</div>
        </div>
    @endif

    <div class="lines">
        @forelse($lines as $l)
            <div class="line">
                <div class="name">{{ $l->product_name ?? $l->sku }}</div>
                <div class="qty">× {{ (int)$l->qty }}</div>
                <div class="price">
                    ₺{{ number_format((float)$l->unit_price, 2, ',', '.') }}<br>
                    <span class="muted">Toplam: ₺{{ number_format((float)$l->line_total, 2, ',', '.') }}</span>
                </div>
            </div>
        @empty
            <p class="muted" style="padding:12px 0;">Sipariş satırı bulunamadı.</p>
        @endforelse

        <div class="total">
            <div>Genel Toplam:</div>
            <div>₺{{ number_format((float)$order->total, 2, ',', '.') }}</div>
        </div>
    </div>

    <div style="margin-top:18px;">
        <a class="btn" href="{{ route('shop') }}">← Alışverişe Devam Et</a>
    </div>
</div>
</body>
</html>
