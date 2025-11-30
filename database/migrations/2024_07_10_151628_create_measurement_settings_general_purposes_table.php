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
        Schema::create('measurement_settings_general_purposes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_general_purpose_id')->nullable();
            // Body Temp
            $table->float('body_temp_caution_min');
            $table->float('body_temp_caution_max');
            $table->float('body_temp_danger_min');
            $table->float('body_temp_danger_max');
            // Heart Rate
            $table->float('heart_rate_caution_min');
            $table->float('heart_rate_caution_max');
            $table->float('heart_rate_danger_min');
            $table->float('heart_rate_danger_max');
            // Respiratory Rate
            $table->float('respiratory_rate_caution_min');
            $table->float('respiratory_rate_caution_max');
            $table->float('respiratory_rate_danger_min');
            $table->float('respiratory_rate_danger_max');
            // Blood Pressure - Systolic
            $table->float('blood_pressure_systolic_caution_min');
            $table->float('blood_pressure_systolic_caution_max');
            $table->float('blood_pressure_systolic_danger_min');
            $table->float('blood_pressure_systolic_danger_max');
            // Blood Pressure - Diastolic
            $table->float('blood_pressure_diastolic_caution_min');
            $table->float('blood_pressure_diastolic_caution_max');
            $table->float('blood_pressure_diastolic_danger_min');
            $table->float('blood_pressure_diastolic_danger_max');
            // Oxygen Saturation
            $table->float('oxygen_saturation_caution_min');
            $table->float('oxygen_saturation_caution_max');
            // External Temperature Criteria
            $table->float('external_temp_default_value');             
            $table->timestamps();

            $table->foreign('user_general_purpose_id', 'maes_setting_user_id_fk')
            ->references('id')
            ->on('user_general_purposes')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('measurement_settings_general_purposes');
    }
};