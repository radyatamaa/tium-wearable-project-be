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
        Schema::create('user_general_purposes', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('nickname');
            $table->string('password');
            $table->string('address')->nullable();
            $table->string('receive_email')->default('No');
            $table->string('receive_sms')->default('No');
            $table->string('data_output')->default('No');
            $table->string('member_status')->nullable();
            $table->string('user_type');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_general_purposes');
    }
};