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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_invoice')->unique();
            $table->decimal('total_pembayaran', 15, 2);
            $table->decimal('total_pajak', 15, 2);
            $table->enum('payment_method', ['cash', 'online']);
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'expired', 'refund'])->default('pending');
            $table->string('snap_token')->nullable();
            $table->string('midtrans_transaction_id')->nullable();
            $table->string('midtrans_payment_type')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
