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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('nama_usaha');
            $table->string('nama_pemilik');
            $table->string('nama_aplikasi');
            $table->string('deskripsi');
            $table->string('alamat');
            $table->string('telepon');
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('email');
            $table->string('midtrans_merchant_id')->nullable();
            $table->string('midtrans_client_key')->nullable();
            $table->string('midtrans_server_key')->nullable();
            $table->boolean('midtrans_environment')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
