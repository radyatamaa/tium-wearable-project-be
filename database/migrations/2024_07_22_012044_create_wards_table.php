<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ward_public_welfare', function (Blueprint $table) {
            $table->id();
            $table->string('ward_number');
            $table->string('ward_name');
            $table->boolean('usage_status');
            $table->timestamp('registration_date')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('user_general_purpose_id')->nullable();
            $table->foreign('user_general_purpose_id', 'ward_ugp_id_fk')
            ->references('id')->on('user_general_purposes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ward_public_welfare');
    }
}