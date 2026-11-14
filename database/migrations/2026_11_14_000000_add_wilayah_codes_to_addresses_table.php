<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('province_code', 2)->nullable();
            $table->string('city_code', 5)->nullable();
            $table->string('district_code', 8)->nullable();
            $table->string('village_code', 13)->nullable();
            $table->string('village', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn(['province_code', 'city_code', 'district_code', 'village_code', 'village']);
        });
    }
};
