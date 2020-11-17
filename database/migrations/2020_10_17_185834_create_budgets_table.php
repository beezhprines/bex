<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBudgetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("budgets", function (Blueprint $table) {
            $table->id();

            $table->date("date");
            $table->float("amount")->default(0)->comment("in KZT");
            $table->json("json")->nullable()->comment("json object with different values");
            $table->boolean("paid")->default(false);

            $table->unsignedInteger("budget_type_id");

            $table->timestamps();
            $table->softDeletes();

            $table->index("date");
            $table->index("budget_type_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("budgets");
    }
}
