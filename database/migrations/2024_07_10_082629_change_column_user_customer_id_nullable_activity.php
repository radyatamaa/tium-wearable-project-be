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
        Schema::table('blood_pressure_activities', function (Blueprint $table) {
            $table->unsignedBigInteger('user_customer_id')->nullable()->change();
        });

        Schema::table('menstrual_cycle_activities', function (Blueprint $table) {
            $table->unsignedBigInteger('user_customer_id')->nullable()->change();
        });

        Schema::table('oxygen_saturation_activities', function (Blueprint $table) {
            $table->unsignedBigInteger('user_customer_id')->nullable()->change();
        });

        Schema::table('respiratory_rate_activities', function (Blueprint $table) {
            $table->unsignedBigInteger('user_customer_id')->nullable()->change();
        });

        Schema::table('sleep_activities', function (Blueprint $table) {
            $table->unsignedBigInteger('user_customer_id')->nullable()->change();
        });

        Schema::table('user_customer_gateway_clients', function (Blueprint $table) {
            // First, drop the foreign key if it exists
            if (Schema::hasColumn('user_customer_gateway_clients', 'user_customer_id')) {
                // Check if the foreign key exists before attempting to drop it
                $table->dropForeign('gtw_cln_user_cust_id_fk');

                // Then drop the column
                $table->dropColumn('user_customer_id');
            }
        });      
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blood_pressure_activities', function (Blueprint $table) {
            $table->unsignedBigInteger('user_customer_id');
        });

        Schema::table('menstrual_cycle_activities', function (Blueprint $table) {
            $table->unsignedBigInteger('user_customer_id');
        });

        Schema::table('oxygen_saturation_activities', function (Blueprint $table) {
            $table->unsignedBigInteger('user_customer_id');
        });

        Schema::table('respiratory_rate_activities', function (Blueprint $table) {
            $table->unsignedBigInteger('user_customer_id');
        });

        Schema::table('sleep_activities', function (Blueprint $table) {
            $table->unsignedBigInteger('user_customer_id');
        });

        Schema::table('user_customer_gateway_clients', function (Blueprint $table) {
            // Add the column back
            $table->unsignedBigInteger('user_customer_id');

            // Recreate the foreign key
            $table->foreign('user_customer_id', 'gtw_cln_user_cust_id_fk')
                ->references('id')
                ->on('user_customers')
                ->onDelete('cascade');
        });
    }
};