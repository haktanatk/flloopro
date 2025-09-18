<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BasketItem extends Model
{
    use HasFactory;

    protected $table = 'basket_items';
    protected $fillable = ['basket_id', 'sku', 'qty', 'price', 'name'];

    protected $appends = ['line_total', 'image_url', 'product_name'];
    protected $with = ['productBySku'];

    public function basket()
    {
        return $this->belongsTo(Basket::class, 'basket_id');
    }

    public function productBySku()
    {
        return $this->belongsTo(Product::class, 'sku', 'sku');
    }

    public function getLineTotalAttribute(): float
    {
        return (float) ($this->qty * $this->price);
    }

    public function getProductNameAttribute(): ?string
    {
        if (!empty($this->name)) return $this->name;
        return $this->productBySku?->name;
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->productBySku?->image_url;
    }
}
