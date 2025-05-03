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
            $table->foreignId('unit_id')->constrained('units');
            $table->foreignId('evaluator_id')->constrained('users');
            $table->foreignId('period_id')->constrained('periods');
            $table->decimal('score', 3, 2);
            $table->foreignId('classification_id')->constrained('meta_types');
            $table->foreignId('status_id')->constrained('meta_types');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('evaluations');
    }
};
