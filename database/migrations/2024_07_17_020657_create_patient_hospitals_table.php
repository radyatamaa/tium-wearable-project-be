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
        Schema::create('patient_hospitals', function (Blueprint $table) {
            $table->id();
            $table->string('patient_code')->nullable();
            $table->string('name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->integer('age')->nullable();
            $table->integer('height_cm')->nullable();
            $table->integer('weight_kg')->nullable();
            $table->string('detail_location_id')->nullable();
            $table->string('doctor_in_charge')->nullable();
            $table->string('nurse_in_charge')->nullable();
            $table->string('diagnostic_name')->nullable();
            $table->string('device_code')->nullable();
            $table->string('contact_information')->nullable();
            $table->string('emergency_contact_1')->nullable();
            $table->string('emergency_contact_2')->nullable();
            
            $table->unsignedBigInteger('user_general_purpose_id')->nullable();
            $table->foreign('user_general_purpose_id','ugdp_foreign')->references('id')
            ->on('user_general_purposes')->onDelete('cascade');

            $table->unsignedBigInteger('detailed_location_id')->nullable();
            $table->foreign('detailed_location_id','detailed_loc_foreign')->references('id')
            ->on('detailed_locations')->onDelete('cascade');

            $table->unsignedBigInteger('sub_location_id')->nullable();
            $table->foreign('sub_location_id','sub_loc_foreign')->references('id')
            ->on('sub_locations')->onDelete('cascade');

            $table->unsignedBigInteger('top_location_id')->nullable();
            $table->foreign('top_location_id','top_loc_foreign')->references('id')
            ->on('top_locations')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_hospitals');
    }
};