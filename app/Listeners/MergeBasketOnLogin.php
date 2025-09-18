<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\Basket;

class MergeBasketOnLogin
{
    public function handle(Login $event): void
    {
        $userId = $event->user->id ?? null;
        if (!$userId) return;

        // Anonim sepetin session anahtarı
        $sid = session()->get('cart_sid');
        if (!$sid) return;

        // 1) Session sepetini kullanıcıya bağla
        $basket = Basket::where('status','active')->where('session_id',$sid)->first();
        if (!$basket) return;

        $basket->user_id = $userId;
        $basket->session_id = null;
        $basket->save();

        // 2) Aynı kullanıcıya ait diğer aktif sepetleri birleştir
        $others = Basket::where('status','active')
            ->where('user_id',$userId)
            ->where('id','!=',$basket->id)
            ->get();

        foreach ($others as $o) {
            foreach ($o->items as $it) {
                $ex = $basket->items()->where('sku',$it->sku)->first();
                if ($ex) { $ex->qty += $it->qty; $ex->save(); }
                else {
                    $basket->items()->create([
                        'sku'=>$it->sku,'qty'=>$it->qty,'price'=>$it->price,'name'=>$it->name
                    ]);
                }
            }
            $o->status = 'merged';
            $o->save();
        }
    }
}
