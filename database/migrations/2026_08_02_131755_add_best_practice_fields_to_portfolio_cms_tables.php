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
        Schema::table('tech_stacks', function (Blueprint $table) {
            $table->unsignedInteger('order_no')->default(0)->after('is_active')->index();
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('logo_light_alt')->nullable()->after('logo_light');
            $table->string('logo_dark_alt')->nullable()->after('logo_dark');
            $table->string('meta_title')->nullable()->after('copyright_text');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('og_image')->nullable()->after('meta_description');
        });

        Schema::table('social_media_links', function (Blueprint $table) {
            $table->unsignedInteger('order_no')->default(0)->after('is_active')->index();
        });

        Schema::table('landing_page', function (Blueprint $table) {
            $table->string('meta_title')->nullable()->after('subtitle');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('og_image')->nullable()->after('meta_description');
        });

        Schema::table('landing_page_about_sections', function (Blueprint $table) {
            $table->unsignedInteger('order_no')->default(0)->after('subtitle')->index();
        });

        Schema::table('landing_page_about_items', function (Blueprint $table) {
            $table->unsignedInteger('order_no')->default(0)->after('icon')->index();
        });

        Schema::table('about_page', function (Blueprint $table) {
            $table->string('photo_alt')->nullable()->after('photo');
            $table->string('meta_title')->nullable()->after('photo_alt');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('og_image')->nullable()->after('meta_description');
        });

        Schema::table('about_page_sections', function (Blueprint $table) {
            $table->unsignedInteger('order_no')->default(0)->after('subtitle_rich')->index();
        });

        Schema::table('expertise_sections', function (Blueprint $table) {
            $table->unsignedInteger('order_no')->default(0)->after('subtitle')->index();
        });

        Schema::table('tools', function (Blueprint $table) {
            $table->string('logo_alt')->nullable()->after('logo');
            $table->unsignedInteger('order_no')->default(0)->after('is_active')->index();
        });

        Schema::table('hobby_sections', function (Blueprint $table) {
            $table->unsignedInteger('order_no')->default(0)->after('subtitle')->index();
        });

        Schema::table('hobby_items', function (Blueprint $table) {
            $table->string('photo_alt')->nullable()->after('photo');
            $table->unsignedInteger('order_no')->default(0)->after('photo_alt')->index();
        });

        Schema::table('project_hero', function (Blueprint $table) {
            $table->string('meta_title')->nullable()->after('subtitle');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('og_image')->nullable()->after('meta_description');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->string('thumbnail_alt')->nullable()->after('thumbnail');
            $table->boolean('is_featured')->default(false)->after('is_active')->index();
            $table->timestamp('published_at')->nullable()->after('is_featured')->index();
            $table->string('meta_title')->nullable()->after('live_url');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('og_image')->nullable()->after('meta_description');
        });

        Schema::table('project_core_features', function (Blueprint $table) {
            $table->unsignedInteger('order_no')->default(0)->after('description')->index();
        });

        Schema::table('project_design_highlights', function (Blueprint $table) {
            $table->string('photo_alt')->nullable()->after('photo');
            $table->unsignedInteger('order_no')->default(0)->after('photo_alt')->index();
        });

        Schema::table('notes_hero', function (Blueprint $table) {
            $table->string('meta_title')->nullable()->after('subtitle');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('og_image')->nullable()->after('meta_description');
        });

        Schema::table('notes', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('is_active')->index();
            $table->timestamp('published_at')->nullable()->after('is_featured')->index();
            $table->string('meta_title')->nullable()->after('slug');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('og_image')->nullable()->after('meta_description');
        });

        Schema::table('contact_messages', function (Blueprint $table) {
            $table->string('status')->default('new')->after('message')->index();
            $table->timestamp('read_at')->nullable()->after('status');
            $table->timestamp('replied_at')->nullable()->after('read_at');
        });

        Schema::table('contact_information', function (Blueprint $table) {
            $table->string('image_alt')->nullable()->after('image');
            $table->string('meta_title')->nullable()->after('email');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('og_image')->nullable()->after('meta_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_information', function (Blueprint $table) {
            $table->dropColumn(['image_alt', 'meta_title', 'meta_description', 'og_image']);
        });

        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropColumn(['status', 'read_at', 'replied_at']);
        });

        Schema::table('notes', function (Blueprint $table) {
            $table->dropColumn(['is_featured', 'published_at', 'meta_title', 'meta_description', 'og_image']);
        });

        Schema::table('notes_hero', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description', 'og_image']);
        });

        Schema::table('project_design_highlights', function (Blueprint $table) {
            $table->dropColumn(['photo_alt', 'order_no']);
        });

        Schema::table('project_core_features', function (Blueprint $table) {
            $table->dropColumn('order_no');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['thumbnail_alt', 'is_featured', 'published_at', 'meta_title', 'meta_description', 'og_image']);
        });

        Schema::table('project_hero', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description', 'og_image']);
        });

        Schema::table('hobby_items', function (Blueprint $table) {
            $table->dropColumn(['photo_alt', 'order_no']);
        });

        Schema::table('hobby_sections', function (Blueprint $table) {
            $table->dropColumn('order_no');
        });

        Schema::table('tools', function (Blueprint $table) {
            $table->dropColumn(['logo_alt', 'order_no']);
        });

        Schema::table('expertise_sections', function (Blueprint $table) {
            $table->dropColumn('order_no');
        });

        Schema::table('about_page_sections', function (Blueprint $table) {
            $table->dropColumn('order_no');
        });

        Schema::table('about_page', function (Blueprint $table) {
            $table->dropColumn(['photo_alt', 'meta_title', 'meta_description', 'og_image']);
        });

        Schema::table('landing_page_about_items', function (Blueprint $table) {
            $table->dropColumn('order_no');
        });

        Schema::table('landing_page_about_sections', function (Blueprint $table) {
            $table->dropColumn('order_no');
        });

        Schema::table('landing_page', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description', 'og_image']);
        });

        Schema::table('social_media_links', function (Blueprint $table) {
            $table->dropColumn('order_no');
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['logo_light_alt', 'logo_dark_alt', 'meta_title', 'meta_description', 'og_image']);
        });

        Schema::table('tech_stacks', function (Blueprint $table) {
            $table->dropColumn('order_no');
        });
    }
};
