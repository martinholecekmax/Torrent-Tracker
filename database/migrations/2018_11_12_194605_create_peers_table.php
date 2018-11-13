<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePeersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('peers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('peer_id');
            $table->string('ip');
            $table->integer('port');
            $table->string('info_hash');
            $table->string('event');
            $table->integer('downloaded');
            $table->integer('uploaded');
            $table->integer('left');
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
        Schema::dropIfExists('peers');
    }
}
