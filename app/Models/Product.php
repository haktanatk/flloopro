<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // Eğer fillable gerekiyorsa ekleyebilirsin, mevcut şema için şart değil.

    public function categories()
    {
        return $this->belongsToMany(
            \App\Models\Category::class,
            'product_categories',
            'product_id',
            'category_id'
        );
    }

    public function scopeForGender($q, ?string $gender)
    {
        if (!$gender) return $q;

        return $q->whereHas('categories', function ($c) use ($gender) {
            $c->whereIn('gender_scope', [$gender, 'all']);
        });
    }

    /** SKU ile tek ürün getir (null ise yoktur) */
    public static function findBySku(string $sku): ?self
    {
        return static::where('sku', $sku)->first();
    }

    public static function priceMapBySku(array $skus): array
    {
        return static::whereIn('sku', $skus)->pluck('price','sku')->toArray();
    }

}



