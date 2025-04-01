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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('unit_id')->constrained('units');
            $table->foreignId('evaluator_id')->constrained('users');
            $table->year('period');
            $table->decimal('score', 8, 2);
            $table->foreignId('classification_id')->constrained('meta_types');
            $table->foreignId('status_id')->constrained('meta_types');
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
        Schema::dropIfExists('evaluations');
    }
};
