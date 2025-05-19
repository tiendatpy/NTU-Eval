<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unit_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('units');
            $table->foreignId('quality_id')->constrained('quality');
            $table->foreignId('title_id')->constrained('titles');
            $table->foreignId('reward_id')->constrained('rewards');
            $table->foreignId('period_id')->constrained('periods');
            $table->longText('evidence')->nullable();
            $table->longText('achievement')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unit_evaluations');
    }
};
