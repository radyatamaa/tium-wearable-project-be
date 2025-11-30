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
        Schema::create('patient_call_report_hospitals', function (Blueprint $table) {
            $table->id();
            $table->string('patient_name');
            $table->timestamp('call_occurrence_time');
            $table->string('call_details');
            $table->timestamp('call_confirmation_time')->nullable();
            $table->string('ward');
            $table->string('call_type');
            $table->string('doctor_in_charge');
            $table->string('nurse_in_charge');
            $table->string('medical_staff');
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
        Schema::dropIfExists('patient_call_report_hospitals');
    }
};