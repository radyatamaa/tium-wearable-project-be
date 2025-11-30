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
        Schema::table('user_customers', function (Blueprint $table) {
            $table->string('doctor_name')->nullable();
            $table->string('room_number')->nullable();
        });  
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_customers', function (Blueprint $table) {
            $table->dropColumn('doctor_name');
            $table->dropColumn('room_number');
        });
    }
};