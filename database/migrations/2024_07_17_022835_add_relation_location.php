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
        Schema::table('sub_locations', function (Blueprint $table) {
            $table->unsignedBigInteger('top_location_id')->nullable();
            $table->foreign('top_location_id','top_loc_sub_foreign')->references('id')
            ->on('top_locations')->onDelete('cascade');
        });  

        Schema::table('detailed_locations', function (Blueprint $table) {
            $table->unsignedBigInteger('sub_location_id')->nullable();
            $table->foreign('sub_location_id','sub_loc_det_foreign')->references('id')
            ->on('sub_locations')->onDelete('cascade');
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_locations', function (Blueprint $table) {
            $table->dropColumn('top_location_id');
        });

        Schema::table('detailed_locations', function (Blueprint $table) {
            $table->dropColumn('sub_location_id');
        });
    }
};