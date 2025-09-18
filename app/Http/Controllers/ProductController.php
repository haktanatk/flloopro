<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public function erkekUrunleri()
    {
        $products = Product::forGender('erkek')->distinct()->get();
        return view('pages.erkek', compact('products'));
    }

    public function kadinUrunleri()
    {
        $products = Product::forGender('kadin')->distinct()->get();
        return view('pages.kadin', compact('products'));
    }

    public function cocukUrunleri()
    {
        $products = Product::forGender('cocuk')->distinct()->get();
        return view('pages.cocuk', compact('products'));
    }

    public function show(Product $product, ?string $slug = null)
    {
        $recommended = Product::where('id', '!=', $product->id)
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('pages.product', compact('product', 'recommended'));
    }

    public function urunDetay(Product $product, ?string $slug = null)
    {
        return $this->show($product, $slug);
    }
}
