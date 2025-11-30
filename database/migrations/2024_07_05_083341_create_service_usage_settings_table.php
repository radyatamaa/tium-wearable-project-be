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
        Schema::create('service_usage_settings', function (Blueprint $table) {
            $table->id();
            $table->string('service_classification');
            $table->string('service_classification_desc')->nullable();
            $table->integer('number_of_user_from')->nullable();
            $table->integer('number_of_user_to')->nullable();
            $table->decimal('usage_fee_monthly', 10, 2)->nullable();
            $table->decimal('fees_used_year', 10, 2)->nullable();
            $table->string('service_usage_fee')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_usage_settings');
    }
};