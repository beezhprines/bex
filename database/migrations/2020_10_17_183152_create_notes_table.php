<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();

            $table->string('level')->comment("info, warning, danger");
            $table->string('code');
            $table->string('message');
            $table->text('description')->nullable();
            $table->string('model')->nullable();
            $table->unsignedInteger('model_id')->nullable();

            $table->timestamps();

            $table->index('level');
            $table->index('code');
            $table->index('model');
            $table->index('model_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notes');
    }
}
