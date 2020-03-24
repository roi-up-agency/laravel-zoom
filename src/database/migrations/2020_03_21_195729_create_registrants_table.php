<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegistrantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zoom_registrants', function (Blueprint $table) {
            $table->increments('id');
            $table->string('meeting_id')->index();
            $table->string('occurrence_id')->index();
            $table->string('registrant_id')->index();
            $table->string('email');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('zip')->nullable();
            $table->string('state')->nullable();
            $table->string('phone')->nullable();
            $table->string('industry')->nullable();
            $table->string('org')->nullable();
            $table->string('job_title')->nullable();
            $table->string('purchasing_time_frame')->nullable();
            $table->string('role_in_purchase_process')->nullable();
            $table->string('no_of_employees')->nullable();
            $table->string('comments')->nullable();
            $table->string('custom_questions')->nullable();
            $table->string('join_url')->nullable();
            $table->string('create_time')->nullable();
            $table->string('status')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zoom_registrants');
    }
}
