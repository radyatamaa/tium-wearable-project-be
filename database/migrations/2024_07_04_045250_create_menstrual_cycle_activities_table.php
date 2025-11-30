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
        Schema::create('menstrual_cycle_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_customer_id');
            $table->integer('average_duration');
            $table->integer('average_cycle');
            $table->timestamp('start_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menstrual_cycle_activities');
    }
};