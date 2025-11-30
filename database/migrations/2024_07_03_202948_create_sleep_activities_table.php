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
        Schema::create('sleep_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_customer_id');
            $table->integer('total_sleep');
            $table->integer('awakenings');
            $table->integer('bedtime');
            $table->integer('rem_sleep');
            $table->integer('light_sleep');
            $table->integer('deep_sleep');
            $table->integer('onset_efficiency');
            $table->integer('sleep_efficiency');
            $table->timestamp('recorded_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sleep_activities');
    }
};