<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();

            $table->string('origin_id')->nullable();
            $table->string('title')->nullable();
            $table->unsignedInteger('price')->nullable();
            $table->unsignedInteger('comission')->nullable();
            $table->boolean('conversion')->nullable();

            $table->unsignedInteger('master_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('master_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('services');
    }
}
