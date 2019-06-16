<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('notification_html');
            $table->integer('tweet_id')->nullable();
            $table->integer('user_id');
            $table->integer('type'); // 1 For like, 2 for Comment, and 3 for follow
            $table->integer('extra_id')->nullable(); // I put this to identify which one to delete if a user created many comments and deleted one of them
             // And now I found that I didn't add a feature to delete a comment So I'll leave this as it's untill I add that feature [No intention right now] :'D
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('seen')->default(0);
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
        Schema::dropIfExists('notifications');
    }
}
