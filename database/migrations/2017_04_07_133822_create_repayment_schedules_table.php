<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRepaymentSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repayment_schedules', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('loan_id')->unsigned();
            $table->integer('payment_no')->unsigned();
            $table->datetime('date');
            $table->decimal('payment_amount', 21, 6);
            $table->decimal('principal', 21, 6);
            $table->decimal('interest', 21, 6);
            $table->decimal('balance', 21, 6);

            $table->timestamps();

            $table->foreign('loan_id')->references('id')->on('loans');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('repayment_schedules');
    }
}
