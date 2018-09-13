<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubRedditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_plebbits', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->mediumText('description_social')->nullable();
            $table->string('icon')->nullable();
            $table->string('header')->nullable();
            $table->string('header_type')->nullable()->default('fit');
            $table->text('custom_css')->nullable();
            $table->integer('owner_id')->unsigned();
            $table->string('color')->default('#fff');
            $table->string('header_color')->default('#808080');
            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sub_reddits');
    }
}
