<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIpNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ip_numbers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ip_user_id');
            $table->foreign('ip_user_id')->references('id')->on('users');
            $table->string('ip_num_front');
            $table->string('ip_num_back');
            $table->string('ip_num_port');
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
        Schema::dropIfExists('ip_numbers');
    }
}
