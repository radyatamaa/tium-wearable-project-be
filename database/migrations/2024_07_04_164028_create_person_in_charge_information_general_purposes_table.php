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
        Schema::create('person_in_charge_information_general_purposes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_general_purpose_id');
            $table->string('name')->nullable();
            $table->string('job_title')->nullable();
            $table->string('position')->nullable();
            $table->string('department')->nullable();
            $table->string('direct_number')->nullable();
            $table->string('cell_phone_number')->nullable();
            $table->string('email')->nullable();
            $table->boolean('receive_email')->default(false);
            $table->boolean('receive_sms')->default(false);
            $table->timestamps();

            $table->foreign('user_general_purpose_id', 'per_info_user_id_fk')
            ->references('id')
            ->on('user_general_purposes')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person_in_charge_information_general_purposes');
    }
};