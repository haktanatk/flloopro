{{-- resources/views/pages/orders.blade.php --}}
@includeIf('pages.menu')
@vite(['resources/css/app.css','resources/js/app.js'])


<section class="orders">
    <div class="container">
        <header class="orders__header">
            <h1>Siparişlerim</h1>
            <a href="{{ route('shop') }}" class="btn">Alışverişe dön</a>
        </header>

        @if(session('status'))
            <div class="orders__alert orders__alert--ok">{{ session('status') }}</div>
        @endif
        @if(session('warn'))
            <div class="orders__alert orders__alert--warn">{{ session('warn') }}</div>
        @endif

        <div class="orders__meta">
            Toplam {{ $orders->total() }} sipariş • Bu sayfada tutar:
            ₺{{ number_format((float) $orders->sum('total'), 2, ',', '.') }}
        </div>

        <div class="orders__table">
            <table>
                <thead>
                <tr>
                    <th>No</th>
                    <th>Tarih</th>
                    <th>Tutar</th>
                    <th>Durum</th>
                </tr>
                </thead>
                <tbody>
                @forelse($orders as $o)
                    <tr>
                        <td data-th="No">{{ $o->order_number ?? ('#'.$o->id) }}</td>
                        <td data-th="Tarih">
                            {{ \Illuminate\Support\Carbon::parse($o->created_at)->format('d.m.Y H:i') }}
                        </td>
                        <td data-th="Tutar">₺{{ number_format((float)($o->total ?? 0), 2, ',', '.') }}</td>
                        <td data-th="Durum">
                <span class="badge {{ Str::slug($o->status ?? 'beklemede','-') }}">
                  {{ $o->status ?? 'Beklemede' }}
                </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="orders__empty" colspan="4">Henüz sipariş yok.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="orders__pagination">
            {{ $orders->links() }}
        </div>
    </div>
</section>

<style>
    /* ——— Sadece bu sayfaya özgü minik stil ——— */
    .orders { padding:24px 0; }
    .orders .container { max-width:1000px; margin:0 auto; padding:0 16px; }
    .orders__header { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:10px; }
    .orders__header h1 { font-size:24px; font-weight:600; margin:0; }
    .orders .btn { padding:10px 14px; border:1px solid #e5e7eb; border-radius:10px; background:#fff; text-decoration:none; color:#111; }
    .orders .btn:hover { background:#f9fafb; }

    .orders__meta { color:#6b7280; font-size:14px; margin:8px 0 14px; }

    .orders__alert { margin:12px 0; padding:10px 12px; border-radius:10px; border:1px solid; font-size:14px; }
    .orders__alert--ok   { background:#ecfdf5; border-color:#99f6e4; color:#065f46; }
    .orders__alert--warn { background:#fffbeb; border-color:#fde68a; color:#92400e; }

    .orders__table { background:#fff; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden; }
    .orders__table table { width:100%; border-collapse:collapse; }
    .orders__table th, .orders__table td { padding:12px 14px; border-bottom:1px solid #f1f5f9; text-align:left; }
    .orders__table thead th { background:#f9fafb; font-weight:600; }
    .orders__empty { text-align:center; color:#6b7280; }

    .badge { display:inline-block; padding:4px 10px; font-size:12px; border-radius:999px; border:1px solid rgba(0,0,0,.06); background:#f3f4f6; color:#374151; }
    .badge.tamamlandi, .badge.completed, .badge.paid { background:#ecfdf5; color:#047857; }
    .badge.hazirlaniyor, .badge.processing { background:#eff6ff; color:#1d4ed8; }
    .badge.iptal, .badge.canceled { background:#fef2f2; color:#b91c1c; }

    .orders__pagination { margin-top:14px; display:flex; justify-content:center; }

    /* Mobil tablo – başlıkları hücrelerin üstünde göster */
    @media (max-width: 700px){
        .orders__table table, .orders__table thead, .orders__table tbody, .orders__table th, .orders__table td, .orders__table tr { display:block; }
        .orders__table thead { display:none; }
        .orders__table tr { border-bottom:1px solid #e5e7eb; }
        .orders__table td { border:none; padding:10px 14px; }
        .orders__table td::before { content: attr(data-th); display:block; font-weight:600; color:#6b7280; margin-bottom:4px; }
    }
</style>
