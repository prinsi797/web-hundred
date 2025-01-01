<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppUserDeadliftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_user_deadlifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->integer('deadlift');
            $table->softDeletes();
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
        Schema::dropIfExists('app_user_deadlifts');
    }
}
