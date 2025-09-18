<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Basket extends Model
{
    use HasFactory;

    protected $table = 'baskets';
    protected $fillable = ['user_id','session_id','status'];

    public function items()
    {
        return $this->hasMany(BasketItem::class, 'basket_id');
    }

    public function scopeActive($q) { return $q->where('status','active'); }


    public static function currentFor(Request $request): self
    {
        if (auth()->check()) {
            return static::active()->firstOrCreate(
                ['user_id' => auth()->id()],
                ['session_id' => null, 'status' => 'active']
            );
        }

        $sid = $request->session()->get('cart_sid');
        if (!$sid) {
            $sid = \Illuminate\Support\Str::uuid()->toString();
            $request->session()->put('cart_sid', $sid);
        }

        return static::active()->firstOrCreate(
            ['session_id' => $sid],
            ['user_id' => null, 'status' => 'active']
        );
    }


    public function addOrIncrementItem(string $sku, int $qty, float $price, string $name): BasketItem
    {
        /** @var BasketItem|null $item */
        $item = $this->items()->where('sku', $sku)->first();

        if ($item) {
            $item->qty += $qty;
            $item->save();
            return $item;
        }

        return $this->items()->create([
            'sku'   => $sku,
            'qty'   => $qty,
            'price' => $price,
            'name'  => $name,
        ]);
    }

    /** Sepet toplamını hesapla (model içi) */
    public function total(): float
    {
        return (float) $this->items->sum(fn(BasketItem $i) => $i->line_total);
    }
}
