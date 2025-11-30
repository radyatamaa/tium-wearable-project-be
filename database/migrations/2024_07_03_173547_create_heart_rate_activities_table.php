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
        Schema::create('heart_rate_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_customer_id');
            $table->integer('bpm')->nullable();
            $table->float('hrv')->nullable();
            $table->string('device_address');
            $table->timestamp('recorded_at');
            $table->string('activity_type')->nullable(); // e.g., resting, walking, exercising
            $table->float('cardiac_health_index')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('heart_rate_activities');
    }
};