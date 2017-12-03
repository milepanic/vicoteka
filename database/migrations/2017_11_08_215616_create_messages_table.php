<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->text('content');
            $table->boolean('seen')->default(false);
            $table->timestamps();
        });

        Schema::create('message_user', function (Blueprint $table) {
            $table->integer('user_id');
            $table->integer('message_id');
            $table->integer('reciever_id');
            // primarni kljuc
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
