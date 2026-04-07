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
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // snapshot the address at order time (address may change later)
            $table->string('shipping_full_name');
            $table->string('shipping_phone');
            $table->string('shipping_street');
            $table->string('shipping_city');
            $table->string('shipping_country')->default('Egypt');
            $table->string('shipping_postal_code')->nullable();

            $table->enum('status', [
                'pending',      // just placed
                'confirmed',    // payment confirmed
                'processing',   // being prepared
                'shipped',      // on the way
                'delivered',    // received
                'cancelled',    // cancelled
                'refunded',     // money returned
            ])->default('pending');

            $table->enum('payment_method', [
                'cash_on_delivery',
                'credit_card',
                'paypal',
            ])->default('cash_on_delivery');

            $table->enum('payment_status', [
                'unpaid',
                'paid',
                'refunded',
            ])->default('unpaid');

            $table->decimal('subtotal', 10, 2);      // before discount/shipping
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);         // final amount

            $table->string('notes')->nullable();     // customer notes
            $table->timestamps();
            $table->softDeletes();
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
