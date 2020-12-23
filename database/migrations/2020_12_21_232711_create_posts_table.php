<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->integer('author_id')->nullable();
            $table->integer('image_id')->nullable();
            $table->text('content');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('author_id')->references('id')->on('users')->onDelete('SET NULL');
            $table->foreign('image_id')->references('id')->on('images')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
