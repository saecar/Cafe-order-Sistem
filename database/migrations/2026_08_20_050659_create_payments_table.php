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
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('midtrans_order_id')->nullable()->index();
            $table->string('transaction_id')->nullable()->index();
            $table->string('payment_type')->nullable();
            $table->decimal('gross_amount', 15, 2)->default(0);
            $table->string('transaction_status');
            $table->string('snap_token')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamp('paid_at')->nullable(); 
            $table->timestamps();
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
