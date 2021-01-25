<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRouteLikesTable extends Migration
{
    /**
     * route_likes Table
     *
     * id - 좋아요 레코드 번호
     * route_like_user - 해당 경로에 좋아요를 누른 사용자 id
     * route_like_obj - 좋아요를 받은 해당 경로의 id
     *
     * @return void
     */
    public function up()
    {
        Schema::create('route_likes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('route_like_user');
            $table->foreign('route_like_user')->references('id')->on('users');
            $table->unsignedBigInteger('route_like_obj');
            $table->foreign('route_like_obj')->references('id')->on('routes');
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
        Schema::dropIfExists('route_likes');
    }
}
