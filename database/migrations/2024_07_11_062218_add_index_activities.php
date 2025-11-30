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
        Schema::table('heart_rate_activities', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('recorded_at');
            $table->index('device_address');
        });

        Schema::table('body_temperature_activities', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('recorded_at');
            $table->index('device_address');
        });

        Schema::table('blood_pressure_activities', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('recorded_at');
            $table->index('device_address');
        });

        Schema::table('oxygen_saturation_activities', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('recorded_at');
            $table->index('device_address');
        });

        Schema::table('menstrual_cycle_activities', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('device_address');
        });

        Schema::table('respiratory_rate_activities', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('recorded_at');
            $table->index('device_address');
        });

        Schema::table('sleep_activities', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('recorded_at');
            $table->index('device_address');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('heart_rate_activities', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['recorded_at']);
            $table->dropIndex(['device_address']);
        });

        Schema::table('body_temperature_activities', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['recorded_at']);
            $table->dropIndex(['device_address']);
        });

        Schema::table('blood_pressure_activities', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['recorded_at']);
            $table->dropIndex(['device_address']);
        });

        Schema::table('oxygen_saturation_activities', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['recorded_at']);
            $table->dropIndex(['device_address']);
        });

        Schema::table('menstrual_cycle_activities', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['device_address']);
        });

        Schema::table('respiratory_rate_activities', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['recorded_at']);
            $table->dropIndex(['device_address']);
        });

        Schema::table('sleep_activities', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['recorded_at']);
            $table->dropIndex(['device_address']);
        });
    }
};