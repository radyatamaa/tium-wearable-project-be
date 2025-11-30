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
        Schema::table('sleep_activities', function (Blueprint $table) {
            $table->string('total_sleep')->change();
            $table->string('awakenings')->change();
            $table->string('bedtime')->change();
            $table->string('rem_sleep')->change();
            $table->string('light_sleep')->change();
            $table->string('deep_sleep')->change();
            $table->string('onset_efficiency')->change();
            $table->string('sleep_efficiency')->change();
            $table->string('recorded_at')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sleep_activities', function (Blueprint $table) {
            $table->integer('total_sleep')->change();
            $table->integer('awakenings')->change();
            $table->integer('bedtime')->change();
            $table->integer('rem_sleep')->change();
            $table->integer('light_sleep')->change();
            $table->integer('deep_sleep')->change();
            $table->integer('onset_efficiency')->change();
            $table->integer('sleep_efficiency')->change();
        });
    }
};