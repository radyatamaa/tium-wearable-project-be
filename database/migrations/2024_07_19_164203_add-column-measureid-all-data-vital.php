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
        Schema::table('emergency_report_hospitals', function (Blueprint $table) {
            $table->unsignedBigInteger('measureId')->nullable();
            $table->index('measureId');
        });  

        Schema::table('patient_call_report_hospitals', function (Blueprint $table) {
            $table->unsignedBigInteger('measureId')->nullable();
            $table->index('measureId');
        });  

        Schema::table('emergency_report_general_purposes', function (Blueprint $table) {
            $table->unsignedBigInteger('measureId')->nullable();
            $table->index('measureId');
        });  


        Schema::table('heart_rate_activities', function (Blueprint $table) {
            $table->unsignedBigInteger('measureId')->nullable();
            $table->index('measureId');
        });  
        Schema::table('body_temperature_activities', function (Blueprint $table) {
            $table->unsignedBigInteger('measureId')->nullable();
            $table->index('measureId');
        });  
        Schema::table('blood_pressure_activities', function (Blueprint $table) {
            $table->unsignedBigInteger('measureId')->nullable();
        });  
        Schema::table('oxygen_saturation_activities', function (Blueprint $table) {
            $table->unsignedBigInteger('measureId')->nullable();
            $table->index('measureId');
        });  
        Schema::table('respiratory_rate_activities', function (Blueprint $table) {
            $table->unsignedBigInteger('measureId')->nullable();
            $table->index('measureId');
        });  
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emergency_report_hospitals', function (Blueprint $table) {
            $table->dropIndex(['measureId']);
            $table->dropColumn('measureId');
        });  

        Schema::table('patient_call_report_hospitals', function (Blueprint $table) {
            $table->dropIndex(['measureId']);
            $table->dropColumn('measureId');
        }); 

        Schema::table('emergency_report_general_purposes', function (Blueprint $table) {
            $table->dropIndex(['measureId']);
            $table->dropColumn('measureId');
        }); 

        
        Schema::table('heart_rate_activities', function (Blueprint $table) {
            $table->dropIndex(['measureId']);
            $table->dropColumn('measureId');
        });  
        
        Schema::table('body_temperature_activities', function (Blueprint $table) {
            $table->dropIndex(['measureId']);
            $table->dropColumn('measureId');
        });  

        Schema::table('blood_pressure_activities', function (Blueprint $table) {
            $table->dropIndex(['measureId']);
            $table->dropColumn('measureId');
        });  

        Schema::table('oxygen_saturation_activities', function (Blueprint $table) {
            $table->dropIndex(['measureId']);
            $table->dropColumn('measureId');
        });  

        Schema::table('respiratory_rate_activities', function (Blueprint $table) {
            $table->dropIndex(['measureId']);
            $table->dropColumn('measureId');
        });  
    }
};