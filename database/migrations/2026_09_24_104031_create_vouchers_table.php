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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('discount_type', 20)->comment('percentage, fixed');
            $table->bigInteger('discount_value')->comment('Nilai diskon (persen atau Rupiah)');
            $table->bigInteger('min_purchase')->default(0)->comment('Minimal belanja dalam Rupiah');
            $table->bigInteger('max_discount')->nullable()->comment('Batas max diskon jika percentage');
            $table->integer('usage_limit')->nullable()->comment('Null = unlimited');
            $table->integer('used_count')->default(0);
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('code');
            $table->index(['start_date', 'end_date', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
