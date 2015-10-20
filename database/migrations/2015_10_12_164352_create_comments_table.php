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
            $table->integer('post_id')->unsigned();
            $table->string('comment'); // made unique so as to avoid duplicates
            $table->integer('user_id')->unsigned();
            $table->integer('parent_id')->default(0); // links to parent comment
            $table->string('parents')->nullable();
            $table->softDeletes(); // this adds 'deleted_at' column in the Users table
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users');

            $table->foreign('post_id')
                ->references('id')
                ->on('posts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('comments', function(Blueprint $table) {
            $table->dropForeign('comments_user_id_foreign');
            $table->dropColumn('user_id');
            $table->dropForeign('comments_post_id_foreign');
            $table->dropColumn('post_id');
        });
    }
}