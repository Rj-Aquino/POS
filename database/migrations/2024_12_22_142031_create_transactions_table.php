<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id('TransactionID'); // Primary Key
            $table->unsignedBigInteger('OrderID'); // Foreign Key for Order
            $table->unsignedBigInteger('LoyaltyCardID'); // Foreign Key for Loyalty Card
            $table->integer('TotalPointsUsed'); // Points used
            $table->integer('PointsEarned'); // Points earned
            $table->date('TransactionDate'); // Date of transaction
            $table->timestamps();

            // Define foreign key relationships
            $table->foreign('OrderID')->references('OrderID')->on('orders')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
