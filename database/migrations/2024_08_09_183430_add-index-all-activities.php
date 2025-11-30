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
            $table->index('bpm');
        });

        Schema::table('body_temperature_activities', function (Blueprint $table) {
            $table->index('temperature');
        });

        Schema::table('blood_pressure_activities', function (Blueprint $table) {
            $table->index('systolic');
            $table->index('diastolic');
        });

        Schema::table('oxygen_saturation_activities', function (Blueprint $table) {
            $table->index('saturation');
        });

        Schema::table('respiratory_rate_activities', function (Blueprint $table) {
            $table->index('rate');
        });



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('heart_rate_activities', function (Blueprint $table) {
            $table->dropIndex(['bpm']);
        });

        Schema::table('body_temperature_activities', function (Blueprint $table) {
            $table->dropIndex(['temperature']);
        });

        Schema::table('blood_pressure_activities', function (Blueprint $table) {
            $table->dropIndex(['systolic']);
            $table->dropIndex(['diastolic']);
        });

        Schema::table('oxygen_saturation_activities', function (Blueprint $table) {
            $table->dropIndex(['saturation']);
        });


        Schema::table('respiratory_rate_activities', function (Blueprint $table) {
            $table->dropIndex(['rate']);
        });

    }
};