<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subscription_information_general_purposes', function (Blueprint $table) {
            $table->string('full_address')->nullable();
            $table->string('postal_code')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();

            $table->foreign('city_id')
                ->references('id')->on('master_data_cities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_information_general_purposes', function (Blueprint $table) {
            $table->dropColumn('full_address');
            $table->dropColumn('postal_code');
            $table->dropColumn('city_id');
        });
    }
};