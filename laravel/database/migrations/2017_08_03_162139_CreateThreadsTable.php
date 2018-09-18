<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThreadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('threads', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->unique();
            $table->string('title');
            $table->integer('poster_id')->unsigned();
            $table->integer('reply_count')->default(0);
            $table->integer('upvotes')->default(0);
            $table->integer('downvotes')->default(0);
            $table->integer('score')->default(0);
            $table->integer('sub_lolhow_id')->unsigned();
            $table->string('type')->default('text');
            $table->string('link')->nullable();
            $table->string('media_type')->nullable();
            $table->string('thumbnail')->nullable();
            $table->text('post')->nullable();
            $table->boolean('sticky')->default(0);
            $table->timestamps();

            $table->foreign('poster_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sub_lolhow_id')->references('id')->on('sub_lolhows')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('threads');
    }
}
