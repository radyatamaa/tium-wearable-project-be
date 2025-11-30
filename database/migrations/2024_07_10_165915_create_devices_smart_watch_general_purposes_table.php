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
        Schema::create('devices_smart_watch_general_purposes', function (Blueprint $table) {
            $table->id();
            $table->string('serial');
            $table->string('name');
            $table->string('registered_to');
            $table->boolean('usage_status');
            $table->date('registration_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices_smart_watch_general_purposes');
    }
};