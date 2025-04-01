<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('meta_types', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->foreignId('type_id')->constrained('meta_types');
            $table->foreignId('parent_id')->nullable()->constrained('units');
            $table->timestamps();
        });

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

        Schema::create('unit_heads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->unique()->constrained('units');
            $table->foreignId('head_id')->unique()->constrained('users');
            $table->timestamps();
        });

        Schema::create('evaluation_criteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('meta_types');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('weight', 8, 2);
            $table->timestamps();
        });

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

        Schema::create('evaluation_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluations');
            $table->foreignId('criteria_id')->constrained('evaluation_criteria');
            $table->decimal('score', 8, 2);
            $table->text('comments')->nullable();
        });

        Schema::create('awards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('level_id')->constrained('meta_types');
            $table->timestamps();
        });

        Schema::create('award_nominations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('unit_id')->constrained('units');
            $table->foreignId('award_id')->constrained('awards');
            $table->year('period');
            $table->foreignId('status_id')->constrained('meta_types');
            $table->timestamps();
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type_id')->constrained('meta_types');
            $table->string('file_name');
            $table->string('file_path');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('action');
            $table->string('target_table');
            $table->unsignedBigInteger('target_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('award_nominations');
        Schema::dropIfExists('awards');
        Schema::dropIfExists('evaluation_details');
        Schema::dropIfExists('evaluations');
        Schema::dropIfExists('evaluation_criteria');
        Schema::dropIfExists('unit_heads');
        Schema::dropIfExists('users');
        Schema::dropIfExists('units');
        Schema::dropIfExists('meta_types');
        Schema::dropIfExists('roles');
    }
};
