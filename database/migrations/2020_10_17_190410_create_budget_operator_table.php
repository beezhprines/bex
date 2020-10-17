<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBudgetOperatorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('budget_operator', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('budget_id');
            $table->unsignedInteger('operator_id');

            $table->timestamps();

            $table->index('budget_id');
            $table->index('operator_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('budget_operator');
    }
}
