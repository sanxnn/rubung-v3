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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete()
                ->comment('NULL untuk Custom Order yang tidak ada di katalog');
            $table->string('product_name', 150)->comment('Snapshot nama produk atau deskripsi custom');
            $table->string('variant_name', 100)->comment('Snapshot nama varian atau "Custom"');
            $table->integer('quantity');
            $table->bigInteger('unit_price')->comment('Snapshot harga satuan');
            $table->bigInteger('subtotal')->comment('quantity * unit_price');
            $table->timestamps();

            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
