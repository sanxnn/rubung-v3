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
        Schema::create('product_bundle_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_variant_id')
                ->constrained('product_variants')
                ->cascadeOnDelete()
                ->comment('ID varian Paket');
            $table->foreignId('component_variant_id')
                ->constrained('product_variants')
                ->cascadeOnDelete()
                ->comment('ID varian komponen');
            $table->integer('quantity')->comment('Jumlah komponen dalam 1 paket');
            $table->timestamps();

            // UNIQUE: Mencegah duplikasi komponen dalam 1 paket
            $table->unique(['parent_variant_id', 'component_variant_id'], 'bundle_unique');
            $table->index('parent_variant_id');
            $table->index('component_variant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_bundle_items');
    }
};
