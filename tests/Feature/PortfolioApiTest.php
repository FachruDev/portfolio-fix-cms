<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\NotesCategory;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectCoreFeature;
use App\Models\ProjectDesignHighlight;
use App\Models\SiteSetting;
use App\Models\TechStack;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PortfolioApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'filesystems.media_disk' => 'r2',
            'filesystems.disks.r2.url' => 'https://r2.example.test',
        ]);
    }

    public function test_portfolio_endpoint_returns_lightweight_bootstrap_payload(): void
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
            'title' => 'Featured API',
            'slug' => 'featured-api',
            'thumbnail' => 'projects/thumbnails/api.jpg',
            'is_active' => true,
            'is_featured' => true,
            'published_at' => now(),
        ]);

        $this->getJson('/api/v1/portfolio')
            ->assertOk()
            ->assertHeader('cache-control')
            ->assertJsonPath('data.global.site_settings.site_title', 'Portfolio CMS')
            ->assertJsonPath('data.global.site_settings.logo_light_url', 'https://r2.example.test/logos/light.svg')
            ->assertJsonPath('data.projects_page.featured_projects.0.title', 'Featured API')
            ->assertJsonMissingPath('data.projects_page.categories')
            ->assertJsonMissingPath('data.about_page.sections');
    }

    public function test_projects_endpoint_is_paginated_filterable_and_does_not_include_detail_relations(): void
    {
        $webCategory = ProjectCategory::create([
            'title' => 'Web Apps',
            'slug' => 'web-apps',
            'is_active' => true,
        ]);

        $mobileCategory = ProjectCategory::create([
            'title' => 'Mobile Apps',
            'slug' => 'mobile-apps',
            'is_active' => true,
        ]);

        $project = Project::create([
            'project_category_id' => $webCategory->id,
            'title' => 'Portfolio API',
            'slug' => 'portfolio-api',
            'description_rich' => '<p>Long detail.</p>',
            'thumbnail' => 'projects/thumbnails/api.jpg',
            'is_active' => true,
            'is_featured' => true,
            'published_at' => now(),
        ]);

        Project::create([
            'project_category_id' => $mobileCategory->id,
            'title' => 'Mobile Project',
            'slug' => 'mobile-project',
            'is_active' => true,
            'published_at' => now(),
        ]);

        Project::create([
            'project_category_id' => $webCategory->id,
            'title' => 'Draft Project',
            'slug' => 'draft-project',
            'is_active' => false,
            'published_at' => now(),
        ]);

        $techStack = TechStack::create([
            'name' => 'Laravel',
            'icon' => 'tech/laravel.svg',
            'is_active' => true,
        ]);

        $project->techStacks()->attach($techStack);

        $this->getJson('/api/v1/projects?category=web-apps&featured=1&per_page=1')
            ->assertOk()
            ->assertJsonPath('data.0.title', 'Portfolio API')
            ->assertJsonPath('data.0.category.slug', 'web-apps')
            ->assertJsonPath('data.0.tech_stacks.0.name', 'Laravel')
            ->assertJsonPath('data.0.thumbnail_url', 'https://r2.example.test/projects/thumbnails/api.jpg')
            ->assertJsonPath('meta.per_page', 1)
            ->assertJsonPath('meta.total', 1)
            ->assertJsonMissingPath('data.0.description_rich')
            ->assertJsonMissingPath('data.0.core_features')
            ->assertJsonMissingPath('data.0.design_highlights');
    }

    public function test_project_detail_endpoint_returns_full_published_project_by_slug(): void
    {
        $category = ProjectCategory::create([
            'title' => 'Web Apps',
            'slug' => 'web-apps',
            'is_active' => true,
        ]);

        $project = Project::create([
            'project_category_id' => $category->id,
            'title' => 'Portfolio API',
            'slug' => 'portfolio-api',
            'description_rich' => '<p>Long detail.</p>',
            'thumbnail' => 'projects/thumbnails/api.jpg',
            'is_active' => true,
            'published_at' => now(),
        ]);

        ProjectCoreFeature::create([
            'project_id' => $project->id,
            'description' => 'Public read-only API.',
            'order_no' => 1,
        ]);

        ProjectDesignHighlight::create([
            'project_id' => $project->id,
            'title' => 'Dashboard',
            'photo' => 'projects/highlights/dashboard.jpg',
            'order_no' => 1,
        ]);

        $this->getJson('/api/v1/projects/portfolio-api')
            ->assertOk()
            ->assertJsonPath('data.title', 'Portfolio API')
            ->assertJsonPath('data.description_rich', '<p>Long detail.</p>')
            ->assertJsonPath('data.core_features.0.description', 'Public read-only API.')
            ->assertJsonPath('data.design_highlights.0.photo_url', 'https://r2.example.test/projects/highlights/dashboard.jpg');

        $project->update(['is_active' => false]);

        $this->getJson('/api/v1/projects/portfolio-api')
            ->assertNotFound();
    }

    public function test_notes_endpoint_is_paginated_and_detail_uses_slug(): void
    {
        $category = NotesCategory::create([
            'title' => 'Laravel',
            'slug' => 'laravel',
            'is_active' => true,
        ]);

        Note::create([
            'notes_category_id' => $category->id,
            'title' => 'API Notes',
            'subtitle' => 'Small note',
            'slug' => 'api-notes',
            'is_active' => true,
            'is_featured' => true,
            'published_at' => now(),
        ]);

        Note::create([
            'notes_category_id' => $category->id,
            'title' => 'Draft Notes',
            'slug' => 'draft-notes',
            'is_active' => false,
            'published_at' => now(),
        ]);

        $this->getJson('/api/v1/notes?category=laravel&per_page=5')
            ->assertOk()
            ->assertJsonPath('data.0.title', 'API Notes')
            ->assertJsonPath('data.0.category.slug', 'laravel')
            ->assertJsonPath('meta.total', 1);

        $this->getJson('/api/v1/notes/api-notes')
            ->assertOk()
            ->assertJsonPath('data.title', 'API Notes')
            ->assertJsonPath('data.category.title', 'Laravel');
    }

    public function test_public_portfolio_api_does_not_expose_write_routes(): void
    {
        $this->postJson('/api/v1/projects', [])
            ->assertMethodNotAllowed();

        $this->deleteJson('/api/v1/projects/anything')
            ->assertMethodNotAllowed();
    }

    public function test_public_api_routes_are_not_wrapped_by_auth_or_permission_middleware(): void
    {
        $protectedRoutes = collect(app('router')->getRoutes())
            ->filter(fn ($route): bool => Str::startsWith((string) $route->getName(), 'api.v1.'))
            ->mapWithKeys(function ($route): array {
                $protectedMiddleware = collect($route->gatherMiddleware())
                    ->map(fn (mixed $middleware): string => (string) $middleware)
                    ->filter(fn (string $middleware): bool => Str::startsWith($middleware, ['auth', 'can', 'role', 'permission'])
                        || Str::contains($middleware, ['Authenticate', 'Authorize', 'RoleMiddleware', 'PermissionMiddleware']))
                    ->values()
                    ->all();

                return [$route->getName() => $protectedMiddleware];
            })
            ->filter();

        $this->assertSame([], $protectedRoutes->all());
    }
}
