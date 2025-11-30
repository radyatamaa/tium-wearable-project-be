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
        Schema::create('member_status', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_general_purpose_id')->nullable();
            $table->string('field_of_help')->nullable();
            $table->string('worker')->nullable();
            $table->string('service_of_use')->nullable();
            $table->date('last_payment_date')->nullable();
            $table->date('another_payment_date')->nullable();
            $table->timestamps();
            $table->foreign('user_general_purpose_id', 'member_status_user_id_fk')
            ->references('id')
            ->on('user_general_purposes')
            ->onDelete('cascade');
        });
    }

};