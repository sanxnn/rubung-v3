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
        Schema::create('date_promos', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('Promo 10.10, Tanggal Cantik, dll');
            $table->string('discount_type', 20)->comment('percentage, fixed');
            $table->bigInteger('discount_value');
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['start_datetime', 'end_datetime', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('date_promos');
    }
};
