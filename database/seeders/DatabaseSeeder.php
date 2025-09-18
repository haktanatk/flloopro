<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Tek seferde tüm verileri yükle (depo, ürün, kategori, inventory)
        $this->call([
            DevSeeder::class,
        ]);
    }
}
