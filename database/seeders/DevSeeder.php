<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DevSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $now = now();

            /* ---------------- FK kapat + tabloları temizle ---------------- */
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            DB::table('product_categories')->truncate();
            DB::table('products')->truncate();
            DB::table('categories')->truncate();
            // Envanter tablosu varsa (opsiyonel)
            if (self::tableExists('inventory')) {
                DB::table('inventory')->truncate();
            }
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            /* ---------------- Depolar (opsiyonel) ---------------- */
            if (self::tableExists('stock_sources')) {
                DB::table('stock_sources')->updateOrInsert(['id' => 1], [
                    'code' => '100-1', 'name' => 'Ana Depo', 'type' => 'warehouse',
                    'created_at' => $now, 'updated_at' => $now
                ]);
                DB::table('stock_sources')->updateOrInsert(['id' => 2], [
                    'code' => '100-5', 'name' => 'Merchant', 'type' => 'merchant',
                    'created_at' => $now, 'updated_at' => $now
                ]);
            }

            /* ---------------- Kategoriler (ID’ler sabit) ---------------- */
            DB::table('categories')->insert([
                ['id' => 1, 'slug' => 'erkek', 'name' => 'Erkek', 'gender_scope' => 'erkek', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['id' => 2, 'slug' => 'kadin', 'name' => 'Kadın', 'gender_scope' => 'kadin', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
                ['id' => 3, 'slug' => 'cocuk', 'name' => 'Çocuk', 'gender_scope' => 'cocuk', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ]);

            /* ---------------- Ürünler ---------------- */
            $products = [
                // İlk 3 → ERKEK (cat_id = 1)
                ['slug' => 'ayakkabi',   'name' => 'Ayakkabı',   'sku' => 'SKU001', 'short_desc' => 'Rahat Spor',  'long_desc' => 'Günlük kullanım',   'price' => 299.99, 'stock_qty' => 50, 'image_url' => 'products/ayakkabi.jpg'],
                ['slug' => 'tisort',     'name' => 'Tişört',     'sku' => 'SKU002', 'short_desc' => 'Slim Fit',    'long_desc' => 'Kullanımı rahat',   'price' => 599.99, 'stock_qty' => 50, 'image_url' => 'products/tisort.jpg'],
                ['slug' => 'esofman',    'name' => 'Eşofman',    'sku' => 'SKU003', 'short_desc' => 'Bol paça',    'long_desc' => 'Bol, rahat',        'price' => 499.99, 'stock_qty' => 50, 'image_url' => 'products/esofman.jpg'],

                // 4–6 → KADIN (cat_id = 2)
                ['slug' => 'tayt',       'name' => 'Tayt',       'sku' => 'SKU004', 'short_desc' => 'Rahat geniş', 'long_desc' => 'Kullanımı rahat',   'price' => 399.99, 'stock_qty' => 30, 'image_url' => 'products/tayt.jpg'],
                ['slug' => 'sweatshirt', 'name' => 'Sweatshirt', 'sku' => 'SKU005', 'short_desc' => 'Rahat Spor',  'long_desc' => 'Günlük sıcak',      'price' => 799.99, 'stock_qty' => 50, 'image_url' => 'products/sweatshirt.jpg'],
                ['slug' => 'pantolon',   'name' => 'Pantolon',   'sku' => 'SKU006', 'short_desc' => 'Bol paça',    'long_desc' => 'Rahat kalıp',       'price' => 449.99, 'stock_qty' => 50, 'image_url' => 'products/pantolon.jpg'],

                // 7–9 → ÇOCUK (cat_id = 3)
                ['slug' => 'mayo',       'name' => 'Mayo',       'sku' => 'SKU007', 'short_desc' => 'Pamuklu',     'long_desc' => 'Rahat giyim',       'price' => 249.99, 'stock_qty' => 50, 'image_url' => 'products/mayo.jpg'],
                ['slug' => 'hirka',      'name' => 'Hırka',      'sku' => 'SKU008', 'short_desc' => 'Rahat',       'long_desc' => 'Rahat giyim',       'price' => 149.99, 'stock_qty' => 50, 'image_url' => 'products/hirka.jpg'],
                ['slug' => 'sort',       'name' => 'Şort',       'sku' => 'SKU009', 'short_desc' => 'Rahat kul.',  'long_desc' => 'Rahat Kullanım',    'price' =>  69.99, 'stock_qty' =>  2, 'image_url' => 'products/sort.jpg'],
            ];

            DB::table('products')->insert(array_map(function ($p) use ($now) {
                return array_merge($p, ['is_active' => 1, 'created_at' => $now, 'updated_at' => $now]);
            }, $products));

            /* ---------------- Pivot: product_categories ----------------
               Kural: 1–3 → cat_id=1 (erkek), 4–6 → cat_id=2 (kadın), 7–9 → cat_id=3 (çocuk)
            ---------------------------------------------------------------- */
            $allProducts = DB::table('products')->orderBy('id')->get(['id']);
            $pivotRows   = [];
            foreach ($allProducts as $idx => $row) {
                $i = $idx + 1; // 1-based
                $catId = 1;    // default erkek
                if ($i >= 4 && $i <= 6) $catId = 2;     // kadın
                if ($i >= 7 && $i <= 9) $catId = 3;     // çocuk
                $pivotRows[] = ['product_id' => $row->id, 'category_id' => $catId];
            }
            DB::table('product_categories')->insert($pivotRows);

            /* ---------------- Inventory (opsiyonel) ---------------- */
            if (self::tableExists('inventory')) {
                $all = DB::table('products')->get(['sku', 'stock_qty']);
                foreach ($all as $pp) {
                    $sku = (string)$pp->sku;
                    if ($sku === '') continue;
                    $total = max(0, (int)$pp->stock_qty);
                    $a = intdiv($total, 2);   // Ana Depo
                    $b = $total - $a;         // Merchant
                    foreach ([[1, $a], [2, $b]] as [$src, $qty]) {
                        DB::table('inventory')->updateOrInsert(
                            ['stock_source_id' => $src, 'sku' => $sku],
                            ['on_hand' => $qty, 'reserved' => 0, 'updated_at' => $now, 'created_at' => $now]
                        );
                    }
                }
            }
        });
    }

    private static function tableExists(string $name): bool
    {
        try {
            return \Schema::hasTable($name);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
