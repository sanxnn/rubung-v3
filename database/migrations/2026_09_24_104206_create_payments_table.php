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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->restrictOnDelete()
                ->comment('Non-null, semua payment terhubung ke order');
            $table->string('midtrans_transaction_id', 100)->unique()->nullable()->change();
            $table->string('payment_type', 50)->comment('gopay, bank_transfer, credit_card, dll');
            $table->string('payment_status', 50)->default('pending')
                ->comment('pending, success, failed, expire');
            $table->bigInteger('amount')->comment('Jumlah dalam Rupiah');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('order_id');
            $table->index('payment_status');
            $table->index('midtrans_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
