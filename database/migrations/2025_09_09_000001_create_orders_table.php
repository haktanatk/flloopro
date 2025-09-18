<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('basket_id')->constrained('baskets')->cascadeOnDelete();
            $table->string('order_number')->unique();   // "100-<id>" gibi
            $table->string('status')->default('new');   // new|paid|shipped|canceled
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }
    public function down(): void { Schema::dropIfExists('orders'); }
};
