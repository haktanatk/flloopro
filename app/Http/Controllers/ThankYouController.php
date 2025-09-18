<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ThankYouController extends Controller
{
    public function show(Request $request, ?string $orderNo = null)
    {
        $orderNo = $orderNo ?? $request->query('orderNo') ?? $request->query('order');
        if (!$orderNo) { abort(404, 'Sipariş numarası eksik'); }

        $order = Order::where('order_number', $orderNo)->firstOrFail();

        $lines = OrderLine::leftJoin('products','products.sku','=','order_lines.sku')
            ->where('order_lines.order_id', $order->id)
            ->get([
                'order_lines.order_id',
                'order_lines.sku',
                'order_lines.qty',
                'order_lines.unit_price',
                'order_lines.line_total',
                'order_lines.stock_source_id',
                DB::raw('CONCAT("ORD", order_lines.order_id, "_SRC", order_lines.stock_source_id) AS shipment_ref'),
                'products.name as product_name',
            ]);

        return view('pages.thankyou', compact('order','lines'));
    }
}
