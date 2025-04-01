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
    public function up() {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->foreignId('role_id')->constrained('roles');
            $table->foreignId('unit_id')->nullable()->constrained('units');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('users');
    }
};
