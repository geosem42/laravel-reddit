<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('post_id', 32)->default(0);
            $table->string('comment', 400)->unique(); // made unique so as to avoid duplicates
            $table->integer('user_id')->default(0);
            $table->integer('parent_id')->default(0); // links to parent comment
            $table->string('parents')->nullable();
            $table->softDeletes(); // this adds 'deleted_at' column in the Users table
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
        Schema::drop('comments');
    }
}
