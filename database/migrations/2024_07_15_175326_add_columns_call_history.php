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
        Schema::table('sos_history_calls', function (Blueprint $table) {
            $table->string('name_contact_sos')->nullable();
            $table->string('phone_number_contact_sos')->nullable();
        });  
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sos_history_calls', function (Blueprint $table) {
            $table->dropColumn('name_contact_sos');
            $table->dropColumn('phone_number_contact_sos');
        });
    }
};