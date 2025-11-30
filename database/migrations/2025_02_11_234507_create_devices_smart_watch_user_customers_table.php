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
        Schema::create('devices_smart_watch_user_customers', function (Blueprint $table) {
            $table->id();
            $table->string('device_code')->nullable();
            $table->boolean('usage_status')->default(false);
            $table->unsignedBigInteger('user_customer_id')->nullable();
            $table->timestamps();

            $table->foreign('user_customer_id', 'dswu_foreign')->references('id')->on('user_customers')->onDelete('cascade');
            $table->index('device_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices_smart_watch_user_customers');
    }
};