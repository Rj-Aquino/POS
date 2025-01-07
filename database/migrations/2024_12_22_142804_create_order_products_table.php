<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderProductsTable extends Migration
{
    public function up()
    {
        Schema::create('order_products', function (Blueprint $table) {
            
            $table->unsignedBigInteger('OrderID'); // Foreign Key for Order
            $table->unsignedBigInteger('ProductID'); // Foreign Key for Product
            $table->integer('Quantity'); // Quantity of product
            $table->float('TotalPrice'); // Total price of the product in this order

            $table->primary(['OrderID', 'ProductID']); // Composite Primary Key

            // Define foreign key relationships
            $table->foreign('OrderID')->references('OrderID')->on('orders')->onDelete('cascade');
            // Make sure 'id' is used for the foreign key reference to 'products'
            $table->foreign('ProductID')->references('ProductID')->on('product')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_products');
    }
}
