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
        Schema::rename('user_management_report_public_welfares', 'user_management_public_welfares');
        Schema::table('user_management_public_welfares', function (Blueprint $table) {
            $table->string('email')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('position')->nullable();
            $table->string('unit')->nullable();
            $table->string('contact_information')->nullable();
            $table->string('emergency_contact_1')->nullable();
            $table->string('emergency_contact_2')->nullable();
            $table->string('device_code')->nullable();

            // $table->unsignedBigInteger('user_general_purpose_id')->nullable();
            // $table->foreign('user_general_purpose_id', 'umpw_gen_pur_id_fk')
            //     ->references('id')->on('user_general_purposes')->onDelete('cascade');

        });
    }
};