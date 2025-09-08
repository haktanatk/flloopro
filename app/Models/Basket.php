<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Basket extends Model
{
    use HasFactory;

    // Modelin ilişkili olduğu veritabanı tablosu
    protected $table = 'baskets';

    // Toplu atama (mass assignment) için izin verilen alanlar
    protected $fillable = ['user_id'];

    /**
     * Bir sepetin (basket) birden çok sepet öğesi (basket item) olabilir.
     * Bu, sepet ile sepet öğeleri arasındaki bir-çok ilişkiyi tanımlar.
     */
    public function items()
    {
        return $this->hasMany(BasketItem::class, 'basket_id');
    }

    /**
     * Sepete yeni bir ürün ekler veya mevcut ürünü günceller.
     *
     * @param int $userId Kullanıcı ID'si
     * @param array $productData Ürün verisi (sku, qty, name, price vb.)
     * @return bool
     */
    public static function addItemToCart($userId, $productData)
    {
        // Kullanıcının sepeti var mı kontrol et, yoksa yeni bir sepet oluştur
        $basket = self::firstOrCreate(['user_id' => $userId]);

        // Sepet öğesini bul veya oluştur
        $basketItem = $basket->items()->firstOrNew(['sku' => $productData['sku']]);

        // Eğer ürün sepette zaten varsa miktarını artır, yoksa yeni miktarını ata
        $basketItem->qty = $basketItem->exists ? $basketItem->qty + $productData['qty'] : $productData['qty'];
        $basketItem->price = $productData['price'];
        $basketItem->name = $productData['name'];
        $basketItem->save();

        return true;
    }
}
