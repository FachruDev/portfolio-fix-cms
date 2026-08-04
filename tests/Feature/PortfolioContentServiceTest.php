<?php

namespace Tests\Feature;

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
            'is_active' => true,
            'order_no' => 1,
        ]);

        $publishedProject->techStacks()->attach($techStack);

        $content = app(PortfolioContentService::class)->fresh();

        $projects = $content['projects_page']['categories']->first()->projects;

        $this->assertCount(1, $projects);
        $this->assertSame('Published Project', $projects->first()->title);
        $this->assertTrue($projects->first()->relationLoaded('techStacks'));
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
}
