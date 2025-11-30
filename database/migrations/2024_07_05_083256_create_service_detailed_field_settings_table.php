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
        Schema::create('service_detailed_field_settings', function (Blueprint $table) {
            $table->id();
            $table->string('service_classification');
            $table->string('service_classification_desc');
            $table->text('medical_subject')->nullable();
            $table->string('detailed_field_by_service')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_detailed_field_settings');
    }
};