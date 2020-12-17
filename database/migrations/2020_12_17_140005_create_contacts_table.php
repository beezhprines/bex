<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();

            $table->date('date');
            $table->unsignedInteger('amount')->default(0);

            $table->unsignedInteger('contact_type_id')->nullable();
            $table->unsignedInteger('team_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('contact_type_id');
            $table->index('team_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}
