<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('categories', function (Blueprint $table) {
            $table->id();  // BIGINT AUTO_INCREMENT PRIMARY KEY
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('gender_scope', ['kadin', 'erkek', 'cocuk', 'all'])->default('all');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });


        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku', 100)->nullable()->unique();
            $table->string('short_desc', 500)->nullable();
            $table->text('long_desc')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->unsignedInteger('stock_qty')->default(0);
            $table->string('image_url', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });


        Schema::create('product_categories', function (Blueprint $table) {
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();
            $table->foreignId('category_id')
                ->constrained('categories')
                ->cascadeOnDelete();
            $table->primary(['product_id', 'category_id']);
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};




