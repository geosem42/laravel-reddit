<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentvotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commentvotes', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('value');
            $table->integer('user_id')->unsigned();
            $table->integer('comment_id')->unsigned();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users');

            $table->foreign('comment_id')
                ->references('id')
                ->on('comments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('commentvotes', function(Blueprint $table) {
            $table->dropForeign('commentvotes_user_id_foreign');
            $table->dropColumn('user_id');
            $table->dropForeign('commentvotes_comment_id_foreign');
            $table->dropColumn('comment_id');
        });
    }
}
