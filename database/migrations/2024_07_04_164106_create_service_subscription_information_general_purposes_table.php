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
        Schema::create('service_subscription_information_general_purposes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_general_purpose_id');
            $table->string('subscription_service')->nullable();
            $table->date('start_date_of_use')->nullable();
            $table->date('expiration_date')->nullable();
            $table->integer('number_of_service_users')->nullable();
            $table->integer('usage_period_months')->nullable();
            $table->decimal('service_fee', 8, 2)->nullable();
            $table->timestamps();

            $table->foreign('user_general_purpose_id', 'svc_info_user_id_fk')
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
        Schema::dropIfExists('service_subscription_information_general_purposes');
    }
};