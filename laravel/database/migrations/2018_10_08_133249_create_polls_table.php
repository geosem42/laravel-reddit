<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('polls', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('thread_id')->unsigned();
            $table->integer('sublolhow_id')->unsigned();
            $table->string('title');
            $table->longText('description');
            $table->integer('minimum_karma');
            $table->timestamp('poll_end')->nullable();
            $table->enum('suggestion', ['yes', 'no'])->default('no');
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->enum('type', ['review', 'poll'])->default('poll');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('thread_id')->references('id')->on('threads')->onDelete('cascade');
            $table->foreign('sublolhow_id')->references('id')->on('sub_lolhows')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('polls');
    }
}
