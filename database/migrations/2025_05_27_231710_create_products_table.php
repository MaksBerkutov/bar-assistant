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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('barcode')->nullable(); // Для кулинарии может быть null
            $table->enum('type', ['inventory', 'culinary', 'cocktail','hookah','draft']);
            $table->integer('stock_quantity')->nullable(); // Только для складских товаров
            $table->decimal('price', 8, 2);
            $table->text('photo')->nullable(); // Путь к фото
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
