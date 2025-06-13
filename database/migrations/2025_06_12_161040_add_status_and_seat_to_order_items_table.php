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
        Schema::table('order_items', function (Blueprint $table) {
            $table->enum('culinary_status', ['New', 'InProgress', 'Completed'])->nullable();

            $table->unsignedBigInteger('seat_number')->nullable();
            $table->foreign('seat_number')->references('id')->on('zones')->onDelete('no action');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('culinary_status');
            $table->dropColumn('seat_number');
        });
    }
};
