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
        Schema::table('measurement_settings_general_purposes', function (Blueprint $table) {
            $table->float('oxygen_saturation_danger_min')->nullable();
            $table->float('oxygen_saturation_danger_max')->nullable();
        });

        Schema::table('measurement_settings_public_welfares', function (Blueprint $table) {
            $table->float('oxygen_saturation_danger_min')->nullable();
            $table->float('oxygen_saturation_danger_max')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('measurement_settings_general_purposes', function (Blueprint $table) {
            $table->dropColumn('oxygen_saturation_danger_min');
            $table->dropColumn('oxygen_saturation_danger_max');
        });

        Schema::table('measurement_settings_public_welfares', function (Blueprint $table) {
            $table->dropColumn('oxygen_saturation_danger_min');
            $table->dropColumn('oxygen_saturation_danger_max');
        });
    }
};