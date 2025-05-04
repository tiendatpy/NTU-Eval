<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('title_nominations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('unit_id')->constrained('units');
            $table->foreignId('title_id')->constrained('titles');
            $table->foreignId('approved_title_id')->nullable()->constrained('titles');
            $table->foreignId('period_id')->constrained('periods');
            $table->foreignId('status_id')->constrained('meta_types');
            $table->foreignId('reward_id')->constrained('rewards');
            $table->text('achievement')->nullable();
            $table->text('review')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('title_nominations');
    }
};