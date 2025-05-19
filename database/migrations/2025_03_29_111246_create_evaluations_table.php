<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluator_id')->constrained('users');
            $table->foreignId('unit_id')->constrained('units');
            $table->foreignId('period_id')->constrained('periods');
            $table->integer('rating');
            $table->foreignId('quality_id')->constrained('quality');
            $table->foreignId('approved_quality_id')->nullable()->constrained('quality');
            $table->foreignId('title_id')->constrained('titles');
            $table->foreignId('approved_title_id')->nullable()->constrained('titles');
            $table->foreignId('reward_id')->constrained('rewards');
            $table->longText('achievement');
            $table->longText('comment')->nullable();
            $table->longText('review')->nullable();
            $table->longText('feedback')->nullable();
            $table->foreignId('status_id')->constrained('meta_types');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('evaluations');
    }
};
