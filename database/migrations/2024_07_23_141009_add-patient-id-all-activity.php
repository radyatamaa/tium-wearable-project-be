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
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->foreign('patient_id', 'hrt_pt_id_fk')
                ->references('id')->on('patient_hospitals')->onDelete('cascade');
        });
        Schema::table('body_temperature_activities', function (Blueprint $table) {
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->foreign('patient_id', 'brt_pt_id_fk')
                ->references('id')->on('patient_hospitals')->onDelete('cascade');
        });
        Schema::table('blood_pressure_activities', function (Blueprint $table) {
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->foreign('patient_id', 'bprt_pt_id_fk')
                ->references('id')->on('patient_hospitals')->onDelete('cascade');
        });
        Schema::table('oxygen_saturation_activities', function (Blueprint $table) {
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->foreign('patient_id', 'oxrt_pt_id_fk')
                ->references('id')->on('patient_hospitals')->onDelete('cascade');
        });
        Schema::table('respiratory_rate_activities', function (Blueprint $table) {
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->foreign('patient_id', 'rrrt_pt_id_fk')
                ->references('id')->on('patient_hospitals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('heart_rate_activities', function (Blueprint $table) {
            $table->dropColumn('patient_id');
        });

        Schema::table('body_temperature_activities', function (Blueprint $table) {
            $table->dropColumn('patient_id');
        });

        Schema::table('blood_pressure_activities', function (Blueprint $table) {
            $table->dropColumn('patient_id');
        });

        Schema::table('oxygen_saturation_activities', function (Blueprint $table) {
            $table->dropColumn('patient_id');
        });

        Schema::table('respiratory_rate_activities', function (Blueprint $table) {
            $table->dropColumn('patient_id');
        });
    }
};