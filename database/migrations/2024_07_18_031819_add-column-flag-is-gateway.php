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
        Schema::table('heart_rate_activities', function (Blueprint $table) {
            $table->boolean('is_coming_from_gateway')->nullable();
        });  
        Schema::table('body_temperature_activities', function (Blueprint $table) {
            $table->boolean('is_coming_from_gateway')->nullable();
        });  
        Schema::table('blood_pressure_activities', function (Blueprint $table) {
            $table->boolean('is_coming_from_gateway')->nullable();
        });  
        Schema::table('oxygen_saturation_activities', function (Blueprint $table) {
            $table->boolean('is_coming_from_gateway')->nullable();
        });  
        Schema::table('respiratory_rate_activities', function (Blueprint $table) {
            $table->boolean('is_coming_from_gateway')->nullable();
        });  
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('heart_rate_activities', function (Blueprint $table) {
            $table->dropColumn('is_coming_from_gateway');
        });  
        
        Schema::table('body_temperature_activities', function (Blueprint $table) {
            $table->dropColumn('is_coming_from_gateway');
        });  

        Schema::table('blood_pressure_activities', function (Blueprint $table) {
            $table->dropColumn('is_coming_from_gateway');
        });  

        Schema::table('oxygen_saturation_activities', function (Blueprint $table) {
            $table->dropColumn('is_coming_from_gateway');
        });  

        Schema::table('respiratory_rate_activities', function (Blueprint $table) {
            $table->dropColumn('is_coming_from_gateway');
        });  
    }
};