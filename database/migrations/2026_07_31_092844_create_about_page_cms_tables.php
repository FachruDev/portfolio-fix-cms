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
        Schema::create('about_page', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('subtitle')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
        });

        Schema::create('about_page_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('subtitle_rich')->nullable();
            $table->timestamps();
        });

        Schema::create('about_page_section_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('about_page_section_id')->constrained('about_page_sections')->cascadeOnDelete();
            $table->string('title');
            $table->text('subtitle')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedInteger('order_no')->default(0)->index();
            $table->timestamps();

            $table->index('about_page_section_id');
        });

        Schema::create('expertise_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('subtitle')->nullable();
            $table->timestamps();
        });

        Schema::create('tools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('hobby_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('subtitle')->nullable();
            $table->timestamps();
        });

        Schema::create('hobby_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hobby_section_id')->constrained('hobby_sections')->cascadeOnDelete();
            $table->string('photo');
            $table->timestamps();

            $table->index('hobby_section_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hobby_items');
        Schema::dropIfExists('hobby_sections');
        Schema::dropIfExists('tools');
        Schema::dropIfExists('expertise_sections');
        Schema::dropIfExists('about_page_section_items');
        Schema::dropIfExists('about_page_sections');
        Schema::dropIfExists('about_page');
    }
};
