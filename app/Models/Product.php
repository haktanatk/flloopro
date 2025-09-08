<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{

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
}



