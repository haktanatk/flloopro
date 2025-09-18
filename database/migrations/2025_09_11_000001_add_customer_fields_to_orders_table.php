<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('orders', function (Blueprint $t) {
            $t->string('customer_name')->nullable()->after('status');
            $t->string('email')->nullable()->after('customer_name');
            $t->string('phone')->nullable()->after('email');
            $t->string('address', 500)->nullable()->after('phone');

            $t->index(['email']);
        });
    }
    public function down(): void {
        Schema::table('orders', function (Blueprint $t) {
            $t->dropIndex(['email']);
            $t->dropColumn(['customer_name','email','phone','address']);
        });
    }
};
