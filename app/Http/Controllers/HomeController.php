<?php
// app/Http/Controllers/HomeController.php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    /**
     * Anasayfa: rastgele bir cinsiyet seç ve ürün getir.
     */
    // app/Http/Controllers/HomeController.php
    public function index()
    {
        $products = Product::with('categories')
            ->inRandomOrder()
            ->take(4) // istediğin kadar ürün
            ->get();

        return view('pages.shop', [
            'products' => $products,
            'activeGender' => 'mixed', // istersen kullan
        ]);
    }
    public function kadin() {
        $products = $this->randomProductsByGender('kadin');
        return view('pages.kadin', compact('products'));
    }

    public function erkek() {
        $products = $this->randomProductsByGender('erkek');
        return view('pages.erkek', compact('products'));
    }

    public function cocuk() {
        $products = $this->randomProductsByGender('cocuk');
        return view('pages.cocuk', compact('products'));
    }
    private function randomProductsByGender(string $gender)
    {
        $query = Product::query()
            ->with('categories')
            ->whereHas('categories', function ($q) use ($gender) {
                $q->whereIn('gender_scope', [$gender, 'all']);
            });

        // SABİT SIRALAMA: rastgele değil, id DESC (istersen created_at DESC yapabilirsin)
        $items = $query->orderByDesc('id')->get();

        if ($items->isEmpty()) {
            $fallback = Product::query();

            if (Schema::hasColumn('products', 'is_active')) {
                $fallback->where('is_active', true);
            }

            // Fallback’te de sabit sırala
            $items = $fallback->orderByDesc('id')->get();
        }

        return $items;
    }


}
