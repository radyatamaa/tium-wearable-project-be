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
        Schema::create('devices_hospitals', function (Blueprint $table) {
            $table->id();
            $table->date('date_registration')->nullable();
            $table->string('user_name')->nullable();
            $table->string('device_code')->nullable();
            $table->boolean('usage_status')->default(false); // True for '사용', false for '미사용'
            $table->unsignedBigInteger('user_general_purpose_id')->nullable();

            $table->foreign('user_general_purpose_id','ugdh_foreign')->references('id')
            ->on('user_general_purposes')->onDelete('cascade');

            $table->unsignedBigInteger('detailed_location_id')->nullable();
            $table->foreign('detailed_location_id','detailed_loc_dev_foreign')->references('id')
            ->on('detailed_locations')->onDelete('cascade');

            $table->unsignedBigInteger('sub_location_id')->nullable();
            $table->foreign('sub_location_id','sub_loc_dev_foreign')->references('id')
            ->on('sub_locations')->onDelete('cascade');

            $table->unsignedBigInteger('top_location_id')->nullable();
            $table->foreign('top_location_id','top_loc_dev_foreign')->references('id')
            ->on('top_locations')->onDelete('cascade');
            
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->foreign('patient_id','pat_id_dev_foreign')->references('id')
            ->on('patient_hospitals')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices_hospitals');
    }
};