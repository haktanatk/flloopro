<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('shipments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $t->foreignId('stock_source_id')->constrained('stock_sources')->cascadeOnDelete();
            $t->string('status')->default('new'); // new|packed|shipped
            $t->string('carrier')->nullable();
            $t->string('tracking_no')->nullable();
            $t->timestamps();
            $t->unique(['order_id','stock_source_id']); // her depo için 1 shipment
        });
    }
    public function down(): void { Schema::dropIfExists('shipments'); }
};
