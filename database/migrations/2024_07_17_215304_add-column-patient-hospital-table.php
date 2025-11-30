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
            $table->unsignedBigInteger('doctor_in_charge_id')->nullable();
            $table->foreign('doctor_in_charge_id')->references('id')
            ->on('user_staff_hospitals')->onDelete('cascade');

            $table->unsignedBigInteger('nurse_in_charge_id')->nullable();
            $table->foreign('nurse_in_charge_id')->references('id')
            ->on('user_staff_hospitals')->onDelete('cascade');

            $table->string('hospital_room')->nullable();
            $table->string('gender')->nullable();
            $table->string('status')->nullable();
        });  
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_hospitals', function (Blueprint $table) {
            $table->dropColumn('doctor_in_charge_id');
            $table->dropColumn('nurse_in_charge_id');
            $table->dropColumn('hospital_room');
            $table->dropColumn('gender');
            $table->dropColumn('status');
        }); 
    }
};