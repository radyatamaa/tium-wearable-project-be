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
        Schema::table('measurement_setting_criteria_patient_hospitals', function (Blueprint $table) {
            $table->float('blood_pressure_systolic_good_min')->nullable()->change();
            $table->float('blood_pressure_systolic_good_max')->nullable()->change();
            $table->float('blood_pressure_systolic_caution_min')->nullable()->change();
            $table->float('blood_pressure_systolic_caution_max')->nullable()->change();
            $table->float('blood_pressure_systolic_danger_min')->nullable()->change();
            $table->float('blood_pressure_systolic_danger_max')->nullable()->change();
            $table->float('blood_pressure_diastolic_good_min')->nullable()->change();
            $table->float('blood_pressure_diastolic_good_max')->nullable()->change();
            $table->float('blood_pressure_diastolic_caution_min')->nullable()->change();
            $table->float('blood_pressure_diastolic_caution_max')->nullable()->change();
            $table->float('blood_pressure_diastolic_danger_min')->nullable()->change();
            $table->float('blood_pressure_diastolic_danger_max')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('measurement_setting_criteria_patient_hospitals', function (Blueprint $table) {
            $table->float('blood_pressure_systolic_good_min')->nullable()->change();
            $table->float('blood_pressure_systolic_good_max')->nullable()->change();
            $table->float('blood_pressure_systolic_caution_min')->nullable()->change();
            $table->float('blood_pressure_systolic_caution_max')->nullable()->change();
            $table->float('blood_pressure_systolic_danger_min')->nullable()->change();
            $table->float('blood_pressure_systolic_danger_max')->nullable()->change();
            $table->float('blood_pressure_diastolic_good_min')->nullable()->change();
            $table->float('blood_pressure_diastolic_good_max')->nullable()->change();
            $table->float('blood_pressure_diastolic_caution_min')->nullable()->change();
            $table->float('blood_pressure_diastolic_caution_max')->nullable()->change();
            $table->float('blood_pressure_diastolic_danger_min')->nullable()->change();
            $table->float('blood_pressure_diastolic_danger_max')->nullable()->change();
        });
    }
};