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
            $table->float('ecg_good_min')->nullable();
            $table->float('ecg_good_max')->nullable();
            $table->float('ecg_caution_min')->nullable();
            $table->float('ecg_caution_max')->nullable();
            $table->float('ecg_danger_min')->nullable();
            $table->float('ecg_danger_max')->nullable();
        });


        Schema::table('measurement_settings_public_welfares', function (Blueprint $table) {
            $table->float('ecg_caution_min')->nullable();
            $table->float('ecg_caution_max')->nullable();
            $table->float('ecg_danger_min')->nullable();
            $table->float('ecg_danger_max')->nullable();
        });


        Schema::table('measurement_settings_general_purposes', function (Blueprint $table) {
            $table->float('ecg_caution_min')->nullable();
            $table->float('ecg_caution_max')->nullable();
            $table->float('ecg_danger_min')->nullable();
            $table->float('ecg_danger_max')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('measurement_setting_criteria_patient_hospitals', function (Blueprint $table) {
            $table->dropColumn('ecg_good_min');
            $table->dropColumn('ecg_good_max');
            $table->dropColumn('ecg_caution_min');
            $table->dropColumn('ecg_caution_max');
            $table->dropColumn('ecg_danger_min');
            $table->dropColumn('ecg_danger_max');
        });

        Schema::table('measurement_settings_public_welfares', function (Blueprint $table) {
            $table->dropColumn('ecg_caution_min');
            $table->dropColumn('ecg_caution_max');
            $table->dropColumn('ecg_danger_min');
            $table->dropColumn('ecg_danger_max');
        });

        Schema::table('measurement_settings_general_purposes', function (Blueprint $table) {
            $table->dropColumn('ecg_caution_min');
            $table->dropColumn('ecg_caution_max');
            $table->dropColumn('ecg_danger_min');
            $table->dropColumn('ecg_danger_max');
        });
    }
};