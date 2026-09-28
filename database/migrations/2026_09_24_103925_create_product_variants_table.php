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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('name', 100)->comment('Ukuran atau Nama Paket');
            $table->string('sku', 50)->unique()->comment('Stock Keeping Unit');
            $table->bigInteger('price')->comment('Harga dalam Rupiah');
            $table->integer('stock')->default(0)->comment('Diabaikan jika category.type == batik');
            $table->timestamps();

            $table->index('product_id');
            $table->index('sku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
