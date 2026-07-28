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
        Schema::create('staffs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->enum('role', ['Manager', 'Head Chef', 'Chef', 'Waiter', 'Cashier', 'Bartender'])->default('Waiter');
            $table->enum('status', ['on_shift', 'active', 'off_duty'])->default('active');
            $table->enum('shift', ['Morning', 'Evening', 'Night', 'Full-Day'])->default('Morning');
            $table->decimal('hourly_rate', 8, 2)->default(15.00);
            $table->string('avatar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staffs');
    }
};
