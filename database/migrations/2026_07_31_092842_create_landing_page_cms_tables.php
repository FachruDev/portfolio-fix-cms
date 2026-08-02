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
        Schema::create('landing_page', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('subtitle')->nullable();
            $table->timestamps();
        });

        Schema::create('landing_page_about_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('subtitle')->nullable();
            $table->timestamps();
        });

        Schema::create('landing_page_about_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landing_page_about_section_id')->constrained('landing_page_about_sections')->cascadeOnDelete();
            $table->string('title');
            $table->text('subtitle')->nullable();
            $table->string('icon')->nullable();
            $table->timestamps();

            $table->index('landing_page_about_section_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_page_about_items');
        Schema::dropIfExists('landing_page_about_sections');
        Schema::dropIfExists('landing_page');
    }
};
