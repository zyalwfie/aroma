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
       Schema::table('orders', function (Blueprint $table) {
    $table->string('midtrans_order_id')->nullable();
    $table->string('snap_token')->nullable();
    $table->string('payment_url')->nullable();
    $table->string('payment_method')->nullable();
    $table->string('shipping_method')->nullable();
    $table->string('payment_status')->default('pending');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};
