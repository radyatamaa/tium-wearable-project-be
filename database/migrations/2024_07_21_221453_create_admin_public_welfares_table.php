<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminPublicWelfaresTable extends Migration
{
    public function up()
    {
        Schema::create('admin_public_welfares', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('name');
            $table->string('password')->nullable();
            $table->string('classification')->nullable();
            $table->string('department')->nullable();
            $table->string('contact')->nullable();
            $table->date('registration_date')->nullable();
            $table->unsignedBigInteger('user_general_purpose_id')->nullable();
            $table->boolean('is_administrator_company')->nullable();
            $table->boolean('is_administrator_tium')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_general_purpose_id', 'admin_ugp_id_fk')
                  ->references('id')->on('user_general_purposes')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('admin_public_welfares');
    }
}