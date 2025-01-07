<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id('OrderID'); // Primary key
            $table->date('OrderDate'); // Order date, required
            $table->float('Subtotal')->nullable(); // Subtotal, optional
            $table->float('Total')->nullable(); // Total, optional


            $table->timestamps(); // Created_at and updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
