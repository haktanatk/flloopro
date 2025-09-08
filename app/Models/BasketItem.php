<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BasketItem extends Model
{
    use HasFactory;

    // Modelin ilişkili olduğu veritabanı tablosu
    protected $table = 'basket_items';

    // Toplu atama (mass assignment) için izin verilen alanlar
    protected $fillable = ['basket_id', 'sku', 'qty', 'price', 'name'];

    /**
     * Bir sepet öğesi (basket item) bir sepete (basket) aittir.
     * Bu, sepet öğesi ile sepet arasındaki bir-bir ilişkiyi tanımlar.
     */
    public function basket()
    {
        return $this->belongsTo(Basket::class, 'basket_id');
    }
}
