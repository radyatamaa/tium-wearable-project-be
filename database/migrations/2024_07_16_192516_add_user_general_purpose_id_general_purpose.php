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
        Schema::table('admin_general_purposes', function (Blueprint $table) {
            $table->unsignedBigInteger('user_general_purpose_id')->nullable();
            $table->foreign('user_general_purpose_id','ugp_foreign')->references('id')
            ->on('user_general_purposes')->onDelete('cascade');
            
            $table->boolean('is_administrator_company')->nullable();
            $table->boolean('is_administrator_tium')->nullable();
        });  

        Schema::table('devices_smart_watch_general_purposes', function (Blueprint $table) {
            $table->unsignedBigInteger('user_general_purpose_id')->nullable();
            $table->foreign('user_general_purpose_id','dgp_gen_id_foreign')->references('id')
            ->on('user_general_purposes')->onDelete('cascade');
        });  

        Schema::table('emergency_report_general_purposes', function (Blueprint $table) {
            $table->unsignedBigInteger('user_general_purpose_id')->nullable();
            $table->foreign('user_general_purpose_id','egp_gen_id_foreign')->references('id')
            ->on('user_general_purposes')->onDelete('cascade');
        });  
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_general_purposes', function (Blueprint $table) {
            $table->dropColumn('user_general_purpose_id');
            $table->dropColumn('is_administrator_company');
            $table->dropColumn('is_administrator_tium');
        });

        Schema::table('devices_smart_watch_general_purposes', function (Blueprint $table) {
            $table->dropColumn('user_general_purpose_id');
        });

        Schema::table('emergency_report_general_purposes', function (Blueprint $table) {
            $table->dropColumn('user_general_purpose_id');
        });
    }
};