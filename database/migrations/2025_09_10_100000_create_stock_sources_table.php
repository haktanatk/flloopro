<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('stock_sources', function (Blueprint $t) {
            $t->id();
            $t->string('code')->unique(); // örn: "100-1", "100-5"
            $t->string('name');
            $t->enum('type', ['warehouse','merchant'])->default('warehouse');
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('stock_sources'); }
};
