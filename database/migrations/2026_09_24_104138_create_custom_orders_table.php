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
        Schema::create('custom_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('reference_number', 50)->unique();
            $table->text('description')->comment('Detail kebutuhan custom dari customer');
            $table->bigInteger('estimated_price')->nullable()->comment('Estimasi harga dari admin');
            $table->string('estimated_time', 100)->nullable();
            $table->string('status', 50)->default('pending_review')
                ->comment('pending_review, quoted, approved, rejected, cancelled');
            $table->text('admin_notes')->nullable();

            // UNIQUE: 1 custom order = 1 order
            $table->foreignId('order_id')->nullable()->unique()->constrained('orders')->nullOnDelete()
                ->comment('Diisi saat custom order disetujui dan jadi order');

            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_orders');
    }
};
