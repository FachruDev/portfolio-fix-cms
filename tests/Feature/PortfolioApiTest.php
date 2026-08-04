<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PortfolioApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_next_js_can_fetch_public_portfolio_content(): void
    {
        SiteSetting::create([
            'site_title' => 'Portfolio CMS',
            'logo_light' => 'logos/light.svg',
        ]);

        $category = ProjectCategory::create([
            'title' => 'Web Apps',
            'slug' => 'web-apps',
            'is_active' => true,
        ]);

        Project::create([
            'project_category_id' => $category->id,
            'title' => 'Portfolio API',
            'slug' => 'portfolio-api',
            'thumbnail' => 'projects/thumbnails/api.jpg',
            'is_active' => true,
            'published_at' => now(),
        ]);

        $this->getJson('/api/v1/portfolio')
            ->assertOk()
            ->assertHeader('cache-control')
            ->assertJsonPath('data.global.site_settings.site_title', 'Portfolio CMS')
            ->assertJsonPath('data.global.site_settings.logo_light_url', 'http://localhost:8000/storage/logos/light.svg')
            ->assertJsonPath('data.projects_page.categories.0.projects.0.title', 'Portfolio API')
            ->assertJsonPath('data.projects_page.categories.0.projects.0.thumbnail_url', 'http://localhost:8000/storage/projects/thumbnails/api.jpg');
    }

    public function test_public_portfolio_api_does_not_expose_write_routes(): void
    {
        $this->postJson('/api/v1/portfolio', [])
            ->assertMethodNotAllowed();
    }
}
