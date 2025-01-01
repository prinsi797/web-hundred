<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppUserFriendsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_user_friends', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('app_user_id');
            $table->unsignedBigInteger('app_friend_id');
            $table->string('is_added')->nullable();
            $table->timestamps();

            // Define foreign keys
            $table->foreign('app_user_id')->references('id')->on('app_users')->onDelete('cascade');
            $table->foreign('app_friend_id')->references('id')->on('app_users')->onDelete('cascade');

            // Ensure uniqueness of the combination of app_user_id and app_friend_id
            $table->unique(['app_user_id', 'app_friend_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_user_friends');
    }
}
