<?php

namespace Tests\Feature;

use App\Models\CmsMediaAsset;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\SiteSetting;
use App\Models\TechStack;
use App\Services\PortfolioContentService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PortfolioContentServiceTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_service_returns_published_eager_loaded_portfolio_content(): void
    {
        SiteSetting::create([
            'site_title' => 'Portfolio CMS',
        ]);

        $category = ProjectCategory::create([
            'title' => 'Web Apps',
            'slug' => 'web-apps',
            'is_active' => true,
            'order_no' => 1,
        ]);

        $publishedProject = Project::create([
            'project_category_id' => $category->id,
            'title' => 'Published Project',
            'slug' => 'published-project',
            'thumbnail' => 'projects/thumbnails/published-project.jpg',
            'meta_title' => 'Published Project',
            'meta_description' => 'Published project description.',
            'is_active' => true,
            'is_featured' => true,
            'published_at' => now(),
        ]);

        Project::create([
            'project_category_id' => $category->id,
            'title' => 'Inactive Project',
            'slug' => 'inactive-project',
            'is_active' => false,
            'published_at' => now(),
        ]);

        $techStack = TechStack::create([
            'name' => 'Laravel',
            'icon' => 'tech/laravel.svg',
            'is_active' => true,
            'order_no' => 1,
        ]);

        $publishedProject->techStacks()->attach($techStack);

        $content = app(PortfolioContentService::class)->fresh();

        $projects = $content['projects_page']['categories'][0]['projects'];

        $this->assertCount(1, $projects);
        $this->assertSame('Published Project', $projects[0]['title']);
        $this->assertSame('http://localhost:8000/storage/projects/thumbnails/published-project.jpg', $projects[0]['thumbnail_url']);
        $this->assertSame('Laravel', $projects[0]['tech_stacks'][0]['name']);
        $this->assertSame('http://localhost:8000/storage/tech/laravel.svg', $projects[0]['tech_stacks'][0]['icon_url']);
    }

    public function test_cms_model_changes_flush_cached_portfolio_content(): void
    {
        Cache::put(PortfolioContentService::CacheKey, ['stale' => true], now()->addMinutes(5));

        TechStack::create([
            'name' => 'Laravel',
            'is_active' => true,
        ]);

        $this->assertFalse(Cache::has(PortfolioContentService::CacheKey));
    }

    public function test_media_assets_are_included_in_public_content_payload(): void
    {
        CmsMediaAsset::factory()->create([
            'title' => 'Open Graph Image',
            'collection' => 'seo',
            'file_path' => 'media-library/seo/og-image.jpg',
            'is_active' => true,
        ]);

        CmsMediaAsset::factory()->create([
            'title' => 'Draft Asset',
            'collection' => 'general',
            'file_path' => 'media-library/general/draft.jpg',
            'is_active' => false,
        ]);

        $content = app(PortfolioContentService::class)->fresh();

        $this->assertCount(1, $content['global']['media_assets']);
        $this->assertSame('Open Graph Image', $content['global']['media_assets'][0]['title']);
        $this->assertSame('http://localhost:8000/storage/media-library/seo/og-image.jpg', $content['global']['media_assets'][0]['file_path_url']);
    }
}
