<?php

namespace App\Http\Controllers;

use App\Models\Basket;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    private function currentBasket(Request $request): Basket
    {
        return Basket::currentFor($request);
    }

    public function store(Request $request)
    {
        $sku = (string) $request->input('sku');
        $qty = (int) $request->input('qty', 1);

        if ($qty < 1) {
            return response()->json(['success' => false, 'message' => '0 dan büyük seçilmelidir.'], 422);
        }

        $product = Product::findBySku($sku);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Ürün bulunamadı'], 404);
        }

        $goReq    = $this->getRequest($sku, $qty);
        $url      = 'http://127.0.0.1:3031/api/cart';
        $response = $this->doRequest($url, $goReq);

        if (empty($response['success'])) {
            return response()->json([
                'success' => false,
                'message' => $response['message'] ?? 'Stok hatası'
            ], 400);
        }

        $basket = $this->currentBasket($request);
        $basket->addOrIncrementItem(
            $sku,
            $qty,
            (float) $product->price,
            (string) $product->name
        );

        return $this->index($request);
    }

    public function index(Request $request)
    {
        $basket = $this->currentBasket($request);

        $items = $basket->items()->get()->map(function ($i) {
            return [
                'id'          => $i->id,
                'sku'         => $i->sku,
                'qty'         => $i->qty,
                'unit_price'  => (float)$i->price,
                'line_total'  => (float)$i->line_total,
                'product_name'=> $i->product_name,
                'image_url'   => $i->image_url,
            ];
        });

        $total = (float) $items->sum('line_total');

        return response()->json([
            'success' => true,
            'cart' => [
                'basket_id' => $basket->id,
                'items'     => $items,
                'total'     => $total,
            ]
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        $basket = $this->currentBasket($request);
        $basket->items()->whereKey($id)->delete();
        return $this->index($request);
    }

    public function getRequest($sku, $qty): string
    {
        return json_encode([
            'qty' => $qty ?? 0,
            'sku' => $sku ?? '',
        ], JSON_UNESCAPED_UNICODE);
    }
}
