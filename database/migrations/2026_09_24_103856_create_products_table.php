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

            // DUAL-LINK: category_id (wajib) + subcategory_id (nullable)
            $table->foreignId('category_id')
                ->constrained('categories')
                ->cascadeOnDelete()
                ->comment('WAJIB - Semua produk punya kategori');

            $table->foreignId('subcategory_id')
                ->nullable()
                ->constrained('subcategories')
                ->nullOnDelete()
                ->comment('NULLABLE - Hanya diisi untuk produk Batik');

            $table->string('name', 150);
            $table->string('slug', 150)->unique();
            $table->text('description')->nullable();
            $table->string('image', 255)->nullable()->comment('Path gambar utama produk'); // <--- BARU
            $table->timestamps();

            // Indexes untuk performa query
            $table->index('category_id');
            $table->index('subcategory_id');
            $table->fullText(['name', 'description']); // Untuk pencarian

            // Composite index untuk filter kategori + subkategori
            $table->index(['category_id', 'subcategory_id'], 'category_subcategory_idx');
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


