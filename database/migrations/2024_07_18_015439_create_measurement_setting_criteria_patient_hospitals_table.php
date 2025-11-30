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
        Schema::create('measurement_setting_criteria_patient_hospitals', function (Blueprint $table) {
            $table->id();
            $table->float('blood_pressure_good_health_value1')->nullable();
            $table->float('blood_pressure_good_health_value2')->nullable();
            $table->float('blood_pressure_caution_value1')->nullable();
            $table->float('blood_pressure_caution_value2')->nullable();
            $table->float('blood_pressure_dangers_value1')->nullable();
            $table->float('blood_pressure_dangers_value2')->nullable();
            $table->float('blood_pressure_two_weeks_value1')->nullable();
            $table->float('blood_pressure_two_weeks_value2')->nullable();
            $table->float('heart_rate_good_health_value1')->nullable();
            $table->float('heart_rate_good_health_value2')->nullable();
            $table->float('heart_rate_caution_value1')->nullable();
            $table->float('heart_rate_caution_value2')->nullable();
            $table->float('heart_rate_dangers_value1')->nullable();
            $table->float('heart_rate_dangers_value2')->nullable();
            $table->float('heart_rate_two_weeks_value1')->nullable();
            $table->float('heart_rate_two_weeks_value2')->nullable();
            $table->float('oxygen_saturation_good_health_value1')->nullable();
            $table->float('oxygen_saturation_good_health_value2')->nullable();
            $table->float('oxygen_saturation_caution_value1')->nullable();
            $table->float('oxygen_saturation_caution_value2')->nullable();
            $table->float('oxygen_saturation_dangers_value1')->nullable();
            $table->float('oxygen_saturation_dangers_value2')->nullable();
            $table->float('oxygen_saturation_two_weeks_value1')->nullable();
            $table->float('oxygen_saturation_two_weeks_value2')->nullable();
            $table->float('body_temperature_good_health_value1')->nullable();
            $table->float('body_temperature_good_health_value2')->nullable();
            $table->float('body_temperature_caution_value1')->nullable();
            $table->float('body_temperature_caution_value2')->nullable();
            $table->float('body_temperature_dangers_value1')->nullable();
            $table->float('body_temperature_dangers_value2')->nullable();
            $table->float('body_temperature_two_weeks_value1')->nullable();
            $table->float('body_temperature_two_weeks_value2')->nullable();
            $table->float('respiratory_good_health_value1')->nullable();
            $table->float('respiratory_good_health_value2')->nullable();
            $table->float('respiratory_caution_value1')->nullable();
            $table->float('respiratory_caution_value2')->nullable();
            $table->float('respiratory_dangers_value1')->nullable();
            $table->float('respiratory_dangers_value2')->nullable();
            $table->float('respiratory_two_weeks_value1')->nullable();
            $table->float('respiratory_two_weeks_value2')->nullable();

            $table->unsignedBigInteger('patient_id')->nullable();
            $table->foreign('patient_id','mscph_p_foreign')->references('id')
            ->on('patient_hospitals')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('measurement_setting_criteria_patient_hospitals');
    }
};