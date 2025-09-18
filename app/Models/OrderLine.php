<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderLine extends Model {
    protected $fillable = [
        'order_id','sku','qty','unit_price','line_total','stock_source_id'
    ];

    public function order() { return $this->belongsTo(Order::class); }
}
