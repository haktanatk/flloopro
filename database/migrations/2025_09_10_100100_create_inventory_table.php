<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventory', function (Blueprint $t) {
            $t->id();
            $t->foreignId('stock_source_id')->constrained('stock_sources')->cascadeOnDelete();
            $t->string('sku');
            $t->unsignedInteger('on_hand')->default(0);
            $t->unsignedInteger('reserved')->default(0);
            $t->timestamps();
            $t->unique(['stock_source_id', 'sku']);
            $t->index('sku');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
