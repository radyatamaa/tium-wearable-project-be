<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('heart_rate_activities', function (Blueprint $table) {
            $table->boolean('is_history')->nullable();
        });
        Schema::table('body_temperature_activities', function (Blueprint $table) {
            $table->boolean('is_history')->nullable();
        });
        Schema::table('blood_pressure_activities', function (Blueprint $table) {
            $table->boolean('is_history')->nullable();
        });
        Schema::table('oxygen_saturation_activities', function (Blueprint $table) {
            $table->boolean('is_history')->nullable();
        });
        Schema::table('respiratory_rate_activities', function (Blueprint $table) {
            $table->boolean('is_history')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('heart_rate_activities', function (Blueprint $table) {
            $table->dropColumn('is_history');
        });

        Schema::table('body_temperature_activities', function (Blueprint $table) {
            $table->dropColumn('is_history');
        });

        Schema::table('blood_pressure_activities', function (Blueprint $table) {
            $table->dropColumn('is_history');
        });

        Schema::table('oxygen_saturation_activities', function (Blueprint $table) {
            $table->dropColumn('is_history');
        });

        Schema::table('respiratory_rate_activities', function (Blueprint $table) {
            $table->dropColumn('is_history');
        });
    }
};