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
        Schema::create('cms_media_assets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('collection')->default('general')->index();
            $table->string('disk')->default('public');
            $table->string('file_path')->unique();
            $table->string('file_name')->nullable();
            $table->string('mime_type')->nullable()->index();
            $table->unsignedBigInteger('size')->nullable();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('alt_text')->nullable();
            $table->text('caption')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_media_assets');
    }
};
