<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    protected $fillable = [
        'user_id','basket_id','order_number','status','total',
        'customer_name','email','phone','address',
    ];

    public function lines()
    {
        return $this->hasMany(OrderLine::class);
    }

    public static function createFromAllocations(Basket $basket, array $allocations, array $prices, array $customer = []): self
    {
        return DB::transaction(function () use ($basket, $allocations, $prices, $customer) {

            /** @var self $order */
            $order = static::create([
                'user_id'       => $basket->user_id,
                'basket_id'     => $basket->id,
                'order_number'  => '100-',
                'status'        => 'new',
                'total'         => 0,
                'customer_name' => $customer['full_name'] ?? null,
                'email'         => $customer['email'] ?? null,
                'phone'         => $customer['phone'] ?? null,
                'address'       => $customer['address'] ?? null,
            ]);
            $order->order_number = '100-' . $order->id;
            $order->save();

            $total = 0.0;
            $shipSources = [];

            foreach ($allocations as $al) {
                $sku = (string)($al['sku'] ?? '');
                $qty = (int)($al['qty'] ?? 0);
                $src = (int)($al['stock_source_id'] ?? 0);
                if ($sku === '' || $qty < 1 || $src < 1) {
                    continue;
                }

                $unit      = (float)($prices[$sku] ?? 0);
                $lineTotal = $unit * $qty;

                // order_lines
                $order->lines()->create([
                    'sku'             => $sku,
                    'qty'             => $qty,
                    'unit_price'      => $unit,
                    'line_total'      => $lineTotal,
                    'stock_source_id' => $src,
                ]);
                $total += $lineTotal;
                $shipSources[$src] = true;

                // STOĞU NETLE: aynı depoda satırı kilitle → reserved↓, on_hand↓
                $row = DB::selectOne(
                    'SELECT id, on_hand, reserved
                       FROM inventory
                      WHERE stock_source_id = ? AND sku = ?
                      FOR UPDATE',
                    [$src, $sku]
                );

                if ($row) {
                    $newReserved = max(0, (int)$row->reserved - $qty);
                    $newOnHand   = max(0, (int)$row->on_hand   - $qty);

                    DB::update(
                        'UPDATE inventory SET reserved = ?, on_hand = ? WHERE id = ?',
                        [$newReserved, $newOnHand, (int)$row->id]
                    );
                }
            }

            $order->total = $total;
            $order->save();

            // Her depo için 1 shipment
            foreach (array_keys($shipSources) as $srcId) {
                DB::table('shipments')->updateOrInsert(
                    ['order_id' => $order->id, 'stock_source_id' => $srcId],
                    ['status' => 'new']
                );
            }

            // Sepeti kapat
            $basket->status = 'ordered';
            $basket->save();

            return $order;
        });
    }
}
