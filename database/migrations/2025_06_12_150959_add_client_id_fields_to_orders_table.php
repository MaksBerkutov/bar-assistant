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
            $table->unsignedBigInteger('client_id')->nullable(); // или без nullable, если не допускается NULL
        });

        // Добавляем внешний ключ, ссылающийся на таблицу clients
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('client_id')->references('id')->on('clients')
                ->onDelete('cascade'); // Здесь можно указать onDelete, если нужно (например, cascade)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['client_id']);

        });
    }
};
