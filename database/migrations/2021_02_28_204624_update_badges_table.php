<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBadgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('badges');

        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('badge_user_id');
            $table->foreign('badge_user_id')->references('id')->on('users');
            $table->unsignedBigInteger('badge_type');
            $table->string('badge_name');
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
        //
    }
}
