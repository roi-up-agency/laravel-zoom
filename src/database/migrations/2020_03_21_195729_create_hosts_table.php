<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zoom_hosts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('host_id')->index();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->index();
            $table->text('password');
            $table->integer('type');
            $table->enum('invitation_status', ['pending', 'accepted'])->nullable()->default(null);
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
