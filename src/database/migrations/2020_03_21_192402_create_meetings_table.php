<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zoom_meetings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid')->index()->nullable();
            $table->string('zoom_id')->index()->nullable();
            $table->string('host_id')->index();
            $table->string('topic')->index();
            $table->string('join_url')->nullable();
            $table->integer('type');
            $table->string('start_time');
            $table->integer('duration');
            $table->string('timezone');
            $table->string('agenda')->default("");
            $table->string('password')->nullable();
            $table->text('recurrence')->nullable();
            $table->text('settings');
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
        Schema::dropIfExists('zoom_meetings');
    }
}
