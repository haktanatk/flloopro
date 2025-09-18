<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('order_lines')) {
            Schema::create('order_lines', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
                $table->string('sku');
                $table->unsignedInteger('qty');
                $table->decimal('unit_price', 10, 2)->default(0);
                $table->decimal('line_total', 10, 2)->default(0);
                $table->unsignedBigInteger('stock_source_id')->nullable()->index();
                $table->timestamps();

                $table->index('sku');
            });
        }
    }
    public function down(): void {
        Schema::dropIfExists('order_lines');
    }
};
