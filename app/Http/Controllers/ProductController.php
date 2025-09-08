<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Product;

class ProductController extends Controller
{
    public function erkekUrunleri()
    {
        $products = DB::table('products')
            ->join('product_categories', 'products.id', '=', 'product_categories.product_id')
            ->join('categories', 'categories.id', '=', 'product_categories.category_id')
            ->where('categories.gender_scope', 'erkek')
            ->select('products.*')
            ->distinct()
            ->get();

        return view('pages.erkek', compact('products'));
    }

    public function kadinUrunleri()
    {
        $products = DB::table('products')
            ->join('product_categories', 'products.id', '=', 'product_categories.product_id')
            ->join('categories', 'categories.id', '=', 'product_categories.category_id')
            ->where('categories.gender_scope', 'kadin')
            ->select('products.*')
            ->distinct()
            ->get();

        return view('pages.kadin', compact('products'));
    }

    public function cocukUrunleri()
    {
        $products = DB::table('products')
            ->join('product_categories', 'products.id', '=', 'product_categories.product_id')
            ->join('categories', 'categories.id', '=', 'product_categories.category_id')
            ->where('categories.gender_scope', 'cocuk')
            ->select('products.*')
            ->distinct()
            ->get();

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
