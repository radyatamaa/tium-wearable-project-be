<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeasurementSettingsPublicWelfaresTable extends Migration

{
    public function up()
    {
        Schema::create('measurement_settings_public_welfares', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_general_purpose_id')->nullable();
            $table->float('body_temp_caution_min');
            $table->float('body_temp_caution_max');
            $table->float('body_temp_danger_min');
            $table->float('body_temp_danger_max');
            $table->float('heart_rate_caution_min');
            $table->float('heart_rate_caution_max');
            $table->float('heart_rate_danger_min');
            $table->float('heart_rate_danger_max');
            $table->float('respiratory_rate_caution_min');
            $table->float('respiratory_rate_caution_max');
            $table->float('respiratory_rate_danger_min');
            $table->float('respiratory_rate_danger_max');
            $table->float('blood_pressure_systolic_caution_min');
            $table->float('blood_pressure_systolic_caution_max');
            $table->float('blood_pressure_systolic_danger_min');
            $table->float('blood_pressure_systolic_danger_max');
            $table->float('blood_pressure_diastolic_caution_min');
            $table->float('blood_pressure_diastolic_caution_max');
            $table->float('blood_pressure_diastolic_danger_min');
            $table->float('blood_pressure_diastolic_danger_max');
            $table->float('oxygen_saturation_caution_min');
            $table->float('oxygen_saturation_caution_max');
            $table->float('external_temp_default_value');
            $table->timestamps();

            $table->foreign('user_general_purpose_id', 'mspw_ugp_id_fk')
                  ->references('id')->on('user_general_purposes')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('measurement_settings_public_welfares');
    }
}