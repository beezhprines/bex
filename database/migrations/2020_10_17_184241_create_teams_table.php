<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->unsignedFloat('premium_rate')->nullable()->default(1);

            $table->unsignedInteger('operator_id')->nullable();
            $table->unsignedInteger('city_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('operator_id');
            $table->index('city_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teams');
    }
}
