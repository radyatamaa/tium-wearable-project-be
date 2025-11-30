<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserStaffPublicWelfareTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_staff_public_welfare', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('job_category', 100);
            $table->string('position', 100);
            $table->string('employee_id', 50);
            $table->date('hire_date')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->integer('age')->nullable();
            $table->string('gender', 10)->nullable();
            $table->text('address')->nullable();
            $table->string('contact1', 20)->nullable();
            $table->string('contact2', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('user_id', 100)->nullable();
            $table->string('photo_profile', 100)->nullable();
            $table->string('password', 100)->nullable();
            $table->text('welfare_beneficiary')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('user_general_purpose_id')->nullable();
            $table->foreign('user_general_purpose_id', 'usfpw_ugp_id_fk')
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
        Schema::dropIfExists('user_staff_public_welfare');
    }
}