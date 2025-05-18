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
        // Bảng unit_evaluations
        Schema::table('unit_evaluations', function (Blueprint $table) {
            $table->longText('evidence')->nullable()->change();
            $table->longText('achievement')->change();
        });
        
        // Bảng evaluations
        Schema::table('evaluations', function (Blueprint $table) {
            $table->longText('achievement')->change();
            $table->longText('comment')->nullable()->change();
            $table->longText('review')->nullable()->change();
            $table->longText('feedback')->nullable()->change();
        });
        
        // Bảng evaluation_details
        Schema::table('evaluation_details', function (Blueprint $table) {
            $table->longText('evidence')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Bảng unit_evaluations
        Schema::table('unit_evaluations', function (Blueprint $table) {
            $table->text('evidence')->nullable()->change();
            $table->text('achievement')->change();
        });
        
        // Bảng evaluations
        Schema::table('evaluations', function (Blueprint $table) {
            $table->text('achievement')->change();
            $table->text('comment')->nullable()->change();
            $table->text('review')->nullable()->change();
            $table->text('feedback')->nullable()->change();
        });
        
        // Bảng evaluation_details
        Schema::table('evaluation_details', function (Blueprint $table) {
            $table->text('evidence')->nullable()->change();
        });
    }
};
