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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->boolean('isSuperAdmin')->default(false);
            $table->boolean('canManageEvaluations')->default(false);
            $table->boolean('canApproveEvaluations')->default(false);
            $table->boolean('canExportReports')->default(false);
            $table->boolean('isUnitLeader')->default(false);
            $table->boolean('canManagePeriods')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('roles');
    }
};
