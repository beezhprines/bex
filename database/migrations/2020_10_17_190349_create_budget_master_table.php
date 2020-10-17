<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBudgetMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('budget_master', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('budget_id');
            $table->unsignedInteger('master_id');

            $table->timestamps();

            $table->index('budget_id');
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
        Schema::dropIfExists('budget_master');
    }
}
