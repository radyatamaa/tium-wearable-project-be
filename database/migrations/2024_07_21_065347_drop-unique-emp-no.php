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
        Schema::table('user_staff_hospitals', function (Blueprint $table) {
            $table->dropUnique(['employee_number']); // Menghapus constraint unique dari kolom email
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_staff_hospitals', function (Blueprint $table) {
            $table->unique('employee_number'); // Mengembalikan constraint unique ke kolom email
        });
    }
};