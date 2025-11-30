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
        Schema::create('calories_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_customer_id')->nullable();
            $table->decimal('calories', 5, 2)->nullable();
            $table->decimal('step', 5, 2)->nullable();
            $table->decimal('distance', 5, 2)->nullable();
            $table->timestamp('recorded_at')->nullable();
            $table->string('device_address')->nullable();
            $table->timestamps();

            $table->index('created_at');
            $table->index('recorded_at');
            $table->index('device_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calories_activities');
    }
};