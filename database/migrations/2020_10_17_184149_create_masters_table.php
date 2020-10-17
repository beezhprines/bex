<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('masters', function (Blueprint $table) {
            $table->id();

            $table->string('origin_id')->nullable();
            $table->string('name')->nullable();
            $table->string('specialization')->nullable();
            $table->string('avatar')->nullable();
            $table->date('schedule_till')->nullable();
            $table->unsignedSmallInteger('seance_length')->nullable();

            $table->unsignedInteger('team_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('team_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('masters');
    }
}
