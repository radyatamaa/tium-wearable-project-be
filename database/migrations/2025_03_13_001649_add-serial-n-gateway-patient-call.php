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
        Schema::table('patient_call_report_hospitals', function (Blueprint $table) {
            $table->string('serial_number_gateway', 30)->nullable();
        });

        Schema::table('patient_call_report_general_purposes', function (Blueprint $table) {
            $table->string('serial_number_gateway', 30)->nullable();
        });

        Schema::table('patient_call_report_public_welfares', function (Blueprint $table) {
            $table->string('serial_number_gateway', 30)->nullable();
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