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
        Schema::create('inquiry_for_uses', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('organization');
            $table->string('user_type');
            $table->string('inquirer');
            $table->string('title');
            $table->string('status');
            $table->date('registration_date');
            $table->date('response_date')->nullable();
            $table->string('responder')->nullable();
            $table->text('inquiry_details')->nullable();
            $table->text('response_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiry_for_uses');
    }
};