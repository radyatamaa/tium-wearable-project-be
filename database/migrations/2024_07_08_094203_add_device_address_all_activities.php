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
        Schema::table('blood_pressure_activities', function (Blueprint $table) {
            $table->string('device_address')->nullable();
        });

        Schema::table('menstrual_cycle_activities', function (Blueprint $table) {
            $table->string('device_address')->nullable();
        });

        Schema::table('oxygen_saturation_activities', function (Blueprint $table) {
            $table->string('device_address')->nullable();
        });

        Schema::table('respiratory_rate_activities', function (Blueprint $table) {
            $table->string('device_address')->nullable();
        });

        Schema::table('sleep_activities', function (Blueprint $table) {
            $table->string('device_address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blood_pressure_activities', function (Blueprint $table) {
            $table->dropColumn('device_address');
        });

        Schema::table('menstrual_cycle_activities', function (Blueprint $table) {
            $table->dropColumn('device_address');
        });

        Schema::table('oxygen_saturation_activities', function (Blueprint $table) {
            $table->dropColumn('device_address');
        });

        Schema::table('respiratory_rate_activities', function (Blueprint $table) {
            $table->dropColumn('device_address');
        });

        Schema::table('sleep_activities', function (Blueprint $table) {
            $table->dropColumn('device_address');
        });
    }
};