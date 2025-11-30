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
        Schema::table('all_activity_users', function (Blueprint $table) {
            $table->integer('systolic')->nullable()->change();
            $table->integer('diastolic')->nullable()->change();
            $table->decimal('temperature', 5, 2)->nullable()->change();

            $table->integer('saturation')->nullable()->change();
            $table->string('os_activity_type')->nullable(); // e.g., resting, walking, exercising

            $table->float('hrv')->nullable();
            $table->string('hr_activity_type')->nullable(); // e.g., resting, walking, exercising
            $table->float('cardiac_health_index')->nullable();

            $table->float('respiratory_rate')->nullable();
            $table->string('rr_activity_type')->nullable(); // e.g., resting, walking, exercising

            $table->text('ecg_data')->nullable(); // Assuming ECG data is stored as a text
            $table->float('ecg_speed')->nullable();
            $table->integer('ecg_bpm')->nullable();
            $table->float('ecg_frequency')->nullable();
            $table->string('ecg_activity_type')->nullable(); // e.g., resting, walking, exercising


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};