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
        Schema::table('patient_hospitals', function (Blueprint $table) {
            $table->date('discharge_date')->nullable();
        });  
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_hospitals', function (Blueprint $table) {
            $table->dropColumn('discharge_date');
        });  
    }
};