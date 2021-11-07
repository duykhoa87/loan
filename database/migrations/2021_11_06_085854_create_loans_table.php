<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->mediumInteger('amount')->nullable(true)->default(0);
            $table->double('remain')->nullable(true)->default(0);
            $table->double('weekly_payment')->default(0)->nullable(true);
            $table->integer('loan_term')->default(0)->nullable(true);
            $table->tinyInteger('status')->default(0)->nullable(true);
            $table->integer('created_by')->nullable(true);
            $table->integer('approver')->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loans');
    }
}
