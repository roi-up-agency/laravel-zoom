<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOccurrencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zoom_occurrences', function (Blueprint $table) {
            $table->increments('id');
            $table->string('meeting_id')->index();
            $table->string('occurrence_id')->index();
            $table->string('start_time');
            $table->string('status')->index();
            $table->integer('duration');
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
        Schema::dropIfExists('zoom_occurrences');
    }
}
