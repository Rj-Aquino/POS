<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->string('tokenable_type')->nullable()->change();
            $table->unsignedBigInteger('tokenable_id')->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->string('tokenable_type')->nullable(false)->change();
            $table->unsignedBigInteger('tokenable_id')->nullable(false)->change();
        });
    }
    
};
