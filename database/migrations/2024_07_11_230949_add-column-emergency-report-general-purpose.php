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
        Schema::table('emergency_report_general_purposes', function (Blueprint $table) {
            $table->unsignedBigInteger('user_customer_id')->nullable();
            $table->string('assortment')->nullable();
            $table->string('content')->nullable();
            $table->dateTime('confirmation_time')->nullable();
            $table->dateTime('notification_time')->nullable();
            $table->dateTime('action_time')->nullable();
            
            $table->foreign('user_customer_id', 'emr_rpt_gen_purs_cust_id_fk')
            ->references('id')
            ->on('user_customers')
            ->onDelete('cascade');
        });   


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emergency_report_general_purposes', function (Blueprint $table) {
            $table->dropColumn('user_customer_id');
            $table->dropColumn('assortment');
            $table->dropColumn('content');
            $table->dropColumn('confirmation_time');
            $table->dropColumn('notification_time');
        }); 


    }
};