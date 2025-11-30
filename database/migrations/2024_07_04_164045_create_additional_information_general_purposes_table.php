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
        Schema::create('additional_information_general_purposes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_general_purpose_id');
            $table->string('business_number')->nullable();
            $table->string('corporate_number')->nullable();
            $table->string('type_of_business')->nullable();
            $table->string('business_type')->nullable();
            $table->string('email_receiving_tax_invoice')->nullable();
            $table->boolean('receive_email')->default(false);
            $table->string('data_document_output')->default('No');
            $table->timestamps();

            $table->foreign('user_general_purpose_id', 'add_info_user_id_fk')
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
        Schema::dropIfExists('additional_information_general_purposes');
    }
};