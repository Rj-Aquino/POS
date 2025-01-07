<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Check if the 'product' table exists before creating it
        if (!Schema::hasTable('product')) {
            Schema::create('product', function (Blueprint $table) {
                $table->id('ProductID'); // Primary key
                
                $table->unsignedBigInteger('CategoryID');
                $table->foreign('CategoryID')->references('CategoryID')->on('category')->onDelete('cascade');

                $table->string('Name', 100);
                $table->float('Price');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        // Drop the 'product' table if it exists
        if (Schema::hasTable('product')) {
            Schema::dropIfExists('product');
        }
    }
};
