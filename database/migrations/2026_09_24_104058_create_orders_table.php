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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('address_id')->nullable()->constrained('addresses')->nullOnDelete();
            $table->string('order_number', 50)->unique();

            // Rincian Harga (Transparansi Stacking Promo)
            $table->bigInteger('subtotal')->comment('SUM(order_items.subtotal)');
            $table->bigInteger('discount_date_cantik')->default(0);
            $table->foreignId('date_promo_id')->nullable()->constrained('date_promos')->nullOnDelete();
            $table->bigInteger('discount_voucher')->default(0);
            $table->foreignId('voucher_id')->nullable()->constrained('vouchers')->nullOnDelete();
            $table->bigInteger('shipping_cost')->default(0);
            $table->bigInteger('final_amount')->comment('subtotal - discount + shipping');

            // Status & Pengiriman Manual
            $table->string('status', 50)->default('pending_payment')
                ->comment('pending_payment, paid, processing, packing, shipped, completed, cancelled');
            $table->string('shipping_courier', 100)->nullable()->comment('JNE, J&T, SiCepat, dll');
            $table->string('resi_number', 100)->nullable();

            // Snapshot Alamat (agar histori tidak rusak jika alamat diubah/dihapus)
            $table->string('snapshot_recipient_name', 100);
            $table->string('snapshot_phone', 20);
            $table->string('snapshot_province', 100);
            $table->string('snapshot_city', 100);
            $table->string('snapshot_postal_code', 5);
            $table->text('snapshot_detail');

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('order_number');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

