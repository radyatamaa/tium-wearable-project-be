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
        Schema::create('all_activity_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_customer_id')->nullable();
            $table->integer('systolic');
            $table->integer('diastolic');
            $table->integer('pulse')->nullable();
            $table->timestamp('recorded_at');
            $table->string('device_address')->nullable();
            $table->boolean('is_coming_from_gateway')->nullable();
            $table->unsignedBigInteger('measureId')->nullable();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->boolean('is_history')->nullable();

            $table->decimal('temperature', 5, 2);
            $table->integer('saturation');
            $table->integer('bpm')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('all_activity_users');
    }
};