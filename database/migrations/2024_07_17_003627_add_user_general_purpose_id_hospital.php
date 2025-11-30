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
        Schema::table('user_admin_hospitals', function (Blueprint $table) {
            $table->unsignedBigInteger('user_general_purpose_id')->nullable();
            $table->foreign('user_general_purpose_id','ugph_foreign')->references('id')
            ->on('user_general_purposes')->onDelete('cascade');
            
            $table->boolean('is_administrator_company')->nullable();
            $table->boolean('is_administrator_tium')->nullable();
        });  

        Schema::table('user_staff_hospitals', function (Blueprint $table) {
            $table->unsignedBigInteger('user_general_purpose_id')->nullable();
            $table->foreign('user_general_purpose_id','ush_gen_id_foreign')->references('id')
            ->on('user_general_purposes')->onDelete('cascade');
        });  

        Schema::table('top_locations', function (Blueprint $table) {
            $table->unsignedBigInteger('user_general_purpose_id')->nullable();
            $table->foreign('user_general_purpose_id','top_gen_id_foreign')->references('id')
            ->on('user_general_purposes')->onDelete('cascade');
        });  

        Schema::table('sub_locations', function (Blueprint $table) {
            $table->unsignedBigInteger('user_general_purpose_id')->nullable();
            $table->foreign('user_general_purpose_id','sub_gen_id_foreign')->references('id')
            ->on('user_general_purposes')->onDelete('cascade');
        });  

        Schema::table('detailed_locations', function (Blueprint $table) {
            $table->unsignedBigInteger('user_general_purpose_id')->nullable();
            $table->foreign('user_general_purpose_id','det_gen_id_foreign')->references('id')
            ->on('user_general_purposes')->onDelete('cascade');
        });  
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_admin_hospitals', function (Blueprint $table) {
            $table->dropColumn('user_general_purpose_id');
            $table->dropColumn('is_administrator_company');
            $table->dropColumn('is_administrator_tium');
        });

        Schema::table('user_staff_hospitals', function (Blueprint $table) {
            $table->dropColumn('user_general_purpose_id');
        });

        Schema::table('top_locations', function (Blueprint $table) {
            $table->dropColumn('user_general_purpose_id');
        });

        Schema::table('sub_locations', function (Blueprint $table) {
            $table->dropColumn('user_general_purpose_id');
        });

        Schema::table('detailed_locations', function (Blueprint $table) {
            $table->dropColumn('user_general_purpose_id');
        });
    }
};