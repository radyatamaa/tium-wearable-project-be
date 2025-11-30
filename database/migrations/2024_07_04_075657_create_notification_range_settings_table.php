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
        Schema::create('notification_range_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_customer_id');
            $table->integer('heart_rate_min');
            $table->integer('heart_rate_max');
            $table->decimal('body_temperature_min', 4, 1);
            $table->decimal('body_temperature_max', 4, 1);
            $table->integer('oxygen_saturation_min');
            $table->integer('oxygen_saturation_max');
            $table->integer('blood_pressure_min');
            $table->integer('blood_pressure_max');
            $table->integer('respiration_min');
            $table->integer('respiration_max');
            $table->timestamps();

            $table->foreign('user_customer_id')->references('id')->on('user_customers')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_range_settings');
    }
};