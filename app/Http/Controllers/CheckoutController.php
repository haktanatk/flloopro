<?php

namespace App\Http\Controllers;

use App\Models\Basket;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    private function currentBasket(Request $request): Basket
    {
        return Basket::currentFor($request);
    }

    public function confirm(Request $request)
    {
        $validated = $request->validate([
            'customer.full_name' => ['required','string','min:3'],
            'customer.email'     => ['required','email'],
            'customer.phone'     => ['required','string','min:10'],
            'customer.address'   => ['required','string','min:10'],
        ]);
        $customer = $validated['customer'];

        $basket = $this->currentBasket($request);

        // Sepet satırları
        $items = $basket->items()->get(['sku','qty']);
        if ($items->isEmpty()) {
            return response()->json(['success'=>false,'message'=>'Sepet boş'], 400);
        }

        // 1) GO → /api/stock/reserve
        $reservePayload = json_encode([
            'items' => $items->map(fn($r)=>[
                'sku' => (string)$r->sku,
                'qty' => (int)$r->qty,
            ])->values()->all()
        ], JSON_UNESCAPED_UNICODE);

        $reserveRes = $this->doRequest('http://127.0.0.1:3031/api/stock/reserve', $reservePayload, 'POST');

        if (!is_array($reserveRes) || empty($reserveRes['success'])) {
            return response()->json([
                'success'=>false,
                'message'=>$reserveRes['message'] ?? 'Rezervasyon hatası'
            ], 409);
        }

        // 2) GO cevabını normalize et (KRİTİK: results)
        $rows =
            $reserveRes['allocations']
            ?? ($reserveRes['data']['allocations'] ?? null)
            ?? $reserveRes['results']   // ← Go Fiber handler tam olarak bunu döndürüyor
            ?? $reserveRes['items']
            ?? $reserveRes['lines']
            ?? $reserveRes['result']
            ?? [];

        $allocations = collect(is_array($rows) ? $rows : [])
            ->map(function ($al) {
                $sku = (string)($al['sku'] ?? $al['product_sku'] ?? $al['productSku'] ?? '');
                $qty = (int)   ($al['qty'] ?? $al['quantity'] ?? $al['reserved'] ?? $al['allocated'] ?? 0);
                $src = (int)   ($al['stock_source_id'] ?? $al['stockSourceId'] ?? $al['source_id'] ?? $al['sourceId'] ?? 0);
                return ['sku'=>$sku, 'qty'=>$qty, 'stock_source_id'=>$src];
            })
            ->filter(fn($r) => $r['sku'] !== '' && $r['qty'] > 0 && $r['stock_source_id'] > 0)
            ->values()
            ->all();

        // Sessizce 1’e düşürmek yerine açık hata ver (yanlış depoya yazmayı engeller)
        if (empty($allocations)) {
            return response()->json([
                'success'=>false,
                'message'=>'Depo eşlemesi gelmedi (results boş/uygunsuz)'
            ], 409);
        }

        // 3) Fiyat haritası
        $prices = DB::table('products')->pluck('price', 'sku');

        try {
            $order = Order::createFromAllocations(
                $basket,
                $allocations,
                $prices->toArray(),
                $customer
            );
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Sipariş oluşturulamadı',
            ], 422);
        }

        // 4) 201 + Location
        $url = route('thankyou', ['orderNo' => $order->order_number]); // /tesekkur/100-1

        return response()->json([
            'success'      => true,
            'order_id'     => $order->id,
            'order_number' => $order->order_number,
            'total'        => $order->total,
            'split_sources'=> DB::table('shipments')->where('order_id',$order->id)->count(),
        ], 201)->header('Location', $url);
    }
}
