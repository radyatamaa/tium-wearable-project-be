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
        Schema::create('user_customer_gateway_clients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_customer_id');
            $table->string('serial_number_device');
            $table->string('secret_key');

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_customer_id', 'gtw_cln_user_cust_id_fk')
            ->references('id')
            ->on('user_customers')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_customer_gateway_clients');
    }
};