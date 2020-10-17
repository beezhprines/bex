<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('records', function (Blueprint $table) {
            $table->id();

            $table->string('origin_id')->nullable();
            $table->datetime('started_at')->nullable();
            $table->unsignedInteger('duration')->nullable();
            $table->string('comment')->nullable();
            $table->boolean('attendance');

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
        Schema::dropIfExists('records');
    }
}
