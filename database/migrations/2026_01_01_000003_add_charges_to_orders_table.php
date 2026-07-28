<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('subtotal', 10, 2)->default(0.00)->after('status');
            $table->decimal('delivery_fee', 8, 2)->default(0.00)->after('subtotal');
            $table->decimal('tax_amount', 8, 2)->default(0.00)->after('delivery_fee');
            $table->decimal('discount_amount', 8, 2)->default(0.00)->after('tax_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'delivery_fee', 'tax_amount', 'discount_amount']);
        });
    }
};
