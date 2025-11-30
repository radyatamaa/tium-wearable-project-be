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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('password');
            $table->string('user_id')->unique(); // Nullable if necessary
            $table->text('photo')->nullable(); // Assuming 'photo' is a URL or path
            $table->string('organization_name')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('address')->nullable();
            $table->string('service_of_use')->nullable();
            $table->dateTime('start_date')->nullable(); // Adjust type if needed
            $table->dateTime('date_of_membership')->nullable(); // Adjust type if needed
            $table->string('user_type')->nullable();
            $table->boolean('is_receive_email')->default(false);
            $table->boolean('is_receive_sms')->default(false);
            $table->boolean('is_use_document_data_output')->default(false);
            $table->string('member_status_field_of_help')->nullable();
            $table->string('member_status_worker')->nullable();
            $table->string('member_status_service_of_use')->nullable();
            $table->dateTime('member_status_latest_payment_date')->nullable(); // Adjust type if needed
            $table->dateTime('member_status_another_payment_date')->nullable(); // Adjust type if needed
            $table->string('name_of_representative')->nullable();
            $table->integer('number_of_workers')->nullable();
            $table->string('home_page')->nullable();
            $table->boolean('is_human_resource_management_used')->default(false);
            $table->string('fax_number')->nullable();
            $table->string('field_help')->nullable();
            $table->string('name_of_person_in_charge')->nullable();
            $table->string('job_title')->nullable();
            $table->string('position')->nullable();
            $table->string('affiliated_department')->nullable();
            $table->string('person_in_charge_direct_number')->nullable();
            $table->string('person_in_charge_cell_phone_number')->nullable();
            $table->boolean('person_in_charge_is_agree_to_receive_sms')->default(false);
            $table->string('person_in_charge_email')->nullable();
            $table->boolean('is_agree_to_receive_email')->default(false);
            $table->string('business_number')->nullable();
            $table->string('corporate_number')->nullable();
            $table->string('attachment_file')->nullable();
            $table->string('type_of_business')->nullable();
            $table->string('business_type')->nullable();
            $table->string('email_receiving_tax_invoice')->nullable();
            $table->boolean('additional_is_agree_to_receive_email')->default(false);
            $table->string('data_document_out_put_pdf')->nullable();
            $table->string('subscription_service')->nullable();
            $table->dateTime('start_date_of_use_subscription')->nullable(); // Adjust type if needed
            $table->dateTime('expired_date_subscription')->nullable(); // Adjust type if needed
            $table->string('number_of_service_users')->nullable();
            $table->integer('period_month')->nullable();
            $table->float('service_fee')->nullable(); // Adjust type if needed
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
