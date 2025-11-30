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
        Schema::create('subscription_information_general_purposes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_general_purpose_id');
            $table->date('membership_registration_date')->nullable();
            $table->date('service_start_date')->nullable();
            $table->date('service_expiration_date')->nullable();
            $table->string('representative_contact_info')->nullable();
            $table->string('fax_number')->nullable();
            $table->string('organization_name')->nullable();
            $table->string('address')->nullable();
            $table->string('name_of_representative')->nullable();
            $table->integer('number_of_workers')->nullable();
            $table->string('homepage')->nullable();
            $table->string('hr_management_usage')->default('No');
            $table->timestamps();

            $table->foreign('user_general_purpose_id', 'sub_info_user_id_fk')
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
        Schema::dropIfExists('subscription_information_general_purposes');
    }
};