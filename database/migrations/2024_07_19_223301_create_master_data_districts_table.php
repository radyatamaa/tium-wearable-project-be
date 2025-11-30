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
        Schema::create('master_data_districts', function (Blueprint $table) {
            $table->id();
            $table->string('district_name')->nullable();
            $table->string('district_name_en')->nullable();
            $table->string('postal_code')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();

            $table->foreign('city_id')->references('id')
            ->on('master_data_cities')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_data_districts');
    }
};