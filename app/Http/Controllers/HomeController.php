<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::with('categories')
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('pages.shop', [
            'products' => $products,
            'activeGender' => 'mixed',
        ]);
    }

    public function kadin() { return view('pages.kadin', ['products' => $this->randomProductsByGender('kadin')]); }
    public function erkek() { return view('pages.erkek', ['products' => $this->randomProductsByGender('erkek')]); }
    public function cocuk() { return view('pages.cocuk', ['products' => $this->randomProductsByGender('cocuk')]); }

    private function randomProductsByGender(string $gender)
    {
        $query = Product::query()
            ->with('categories')
            ->forGender($gender);

        $items = $query->orderByDesc('id')->get();

        if ($items->isEmpty()) {
            $fallback = Product::query();
            if (Schema::hasColumn('products', 'is_active')) {
                $fallback->where('is_active', true);
            }
            $items = $fallback->orderByDesc('id')->get();
        }

        return $items;
    }
}
