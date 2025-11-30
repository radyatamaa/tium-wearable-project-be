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
        Schema::create('ecg_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_customer_id');
            $table->text('ecg_data'); // Assuming ECG data is stored as a text
            $table->string('device_address')->nullable();
            $table->timestamp('recorded_at')->nullable();
            $table->string('activity_type')->nullable();
            $table->boolean('is_coming_from_gateway')->default(false);
            $table->string('measureId')->nullable();
            $table->timestamps();

            // Add foreign key constraint if user_customers table exists
            $table->foreign('user_customer_id')->references('id')->on('user_customers')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ecg_activities');
    }
};