<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('project_hero', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('subtitle')->nullable();
            $table->timestamps();
        });

        Schema::create('project_categories', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('order_no')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_category_id')->constrained('project_categories')->cascadeOnDelete();
            $table->string('title');
            $table->text('subtitle')->nullable();
            $table->string('slug')->unique();
            $table->longText('description_rich')->nullable();
            $table->string('thumbnail')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->string('github_url')->nullable();
            $table->string('live_url')->nullable();
            $table->timestamps();

            $table->index('project_category_id');
        });

        Schema::create('project_tech_stack', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('tech_stack_id')->constrained('tech_stacks')->cascadeOnDelete();

            $table->unique(['project_id', 'tech_stack_id']);
            $table->index('tech_stack_id');
        });

        Schema::create('project_core_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->text('description');
            $table->timestamps();

            $table->index('project_id');
        });

        Schema::create('project_design_highlights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('title');
            $table->text('subtitle')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();

            $table->index('project_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_design_highlights');
        Schema::dropIfExists('project_core_features');
        Schema::dropIfExists('project_tech_stack');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('project_categories');
        Schema::dropIfExists('project_hero');
    }
};
