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
            $table->enum('payment_type', ['cash', 'card', 'debt', 'mixed'])->change();
            $table->string('phone')->nullable(); // для долгов
            $table->decimal('cash_amount', 10, 2)->nullable();
            $table->decimal('card_amount', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('payment_type');
            $table->dropColumn('phone');
            $table->dropColumn('cash_amount');
            $table->dropColumn('card_amount');
            $table->enum('payment_type', ['cash', 'card']);


        });
    }
};
