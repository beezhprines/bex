<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecordServiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('record_service', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('record_id');
            $table->unsignedInteger('service_id');

            $table->unsignedFloat('comission')->nullable()->comment('in KZT');
            $table->unsignedFloat('profit')->nullable()->comment('in KZT');

            $table->timestamps();

            $table->index('record_id');
            $table->index('service_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('record_service');
    }
}
