<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrivateMessagingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('private_messages', function(Blueprint $table) {
            $table->increments('id');
            $table->string('code')->unique();
            $table->integer('user_id')->unsigned();
            $table->integer('to_user_id')->unsigned();
            $table->integer('main_msg_id')->nullable();
            $table->string('subject')->nullable();
            $table->text('message');
            $table->boolean('active')->default(1);
            $table->integer('last_pm_timestamp');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('to_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('private_messages');
    }
}
