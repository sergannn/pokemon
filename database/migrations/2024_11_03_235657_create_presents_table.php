<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('presents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->bigInteger('price')->default(0);
            $table->enum('status', ['active', 'inactive']);
            $table->foreignId('user_id')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('presents');
    }
};
