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
            $table->string('sku', 15); //menjadi kode barcode
            $table->string('name', 150);
            $table->foreignId('category_id')
                ->constrained('categories')
                ->onDelete('cascade');
            $table->foreignId('units_id')
                ->constrained('units')
                ->cascadeOnDelete();
            $table->integer('track_stock')->unsigned()->default(0);
            $table->integer('has_expiry')->unsigned()->default(0);
            $table->decimal('cost_price')->nullable();
            $table->decimal('sell_price')->nullable();
            $table->decimal('min_stock')->nullable();
            $table->integer('is_active')->unsigned()->default(1);
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
