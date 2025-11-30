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
        Schema::create('emergency_report_hospitals', function (Blueprint $table) {
            $table->id();
            $table->string('patient_name')->nullable();
            $table->timestamp('alert_occurrence_time')->nullable();
            $table->string('alert_details')->nullable();
            $table->timestamp('alert_confirmation_time')->nullable()->nullable();
            $table->string('ward')->nullable();
            $table->string('alert_type')->nullable();
            $table->string('doctor_in_charge')->nullable();
            $table->string('nurse_in_charge')->nullable();
            $table->string('medical_staff')->nullable();
            $table->text('cause_and_actions')->nullable();

            $table->unsignedBigInteger('user_general_purpose_id')->nullable();
            $table->foreign('user_general_purpose_id')->references('id')
            ->on('user_general_purposes')->onDelete('cascade');
            
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->foreign('patient_id')->references('id')
            ->on('patient_hospitals')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emergency_report_hospitals');
    }
};