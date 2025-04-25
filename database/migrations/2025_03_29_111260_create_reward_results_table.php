<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reward_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('title_id')->constrained('titles');
            $table->foreignId('reward_id')->constrained('rewards');
            $table->date('issued_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reward_results');
    }
};