<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePollResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poll_results', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('poll_id')->unsigned();
            $table->integer('choise_id')->unsigned();
            $table->timestamps();

            $table->foreign('poll_id')->references('id')->on('polls')->onDelete('cascade');
            $table->foreign('choise_id')->references('id')->on('poll_options')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('poll_results');
    }
}
