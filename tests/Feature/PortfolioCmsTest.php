<?php

namespace Tests\Feature;

use App\Models\AboutPage;
use App\Models\ContactInformation;
use App\Models\ContactMessage;
use App\Models\LandingPage;
use App\Models\Note;
use App\Models\NotesCategory;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectCoreFeature;
use App\Models\ProjectHero;
use App\Models\SiteSetting;
use App\Models\TechStack;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PortfolioCmsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_portfolio_cms_records_can_be_created_with_relationships(): void
    {
        $techStack = TechStack::create([
            'name' => 'Laravel',
            'icon' => 'code',
            'is_active' => true,
        ]);

        SiteSetting::create([
            'site_title' => 'Portfolio CMS',
            'copyright_text' => 'Copyright 2026',
        ]);

        LandingPage::create([
            'title' => 'Build better products',
            'subtitle' => 'Selected projects and notes.',
        ]);

        AboutPage::create([
            'title' => 'About Me',
            'subtitle' => 'Full-stack developer.',
        ]);

        ProjectHero::create([
            'title' => 'Projects',
            'subtitle' => 'Selected work.',
        ]);

        $projectCategory = ProjectCategory::create([
            'title' => 'Web Apps',
            'slug' => 'web-apps',
            'is_active' => true,
            'order_no' => 1,
        ]);

        $project = Project::create([
            'project_category_id' => $projectCategory->id,
            'title' => 'Portfolio API',
            'slug' => 'portfolio-api',
            'description_rich' => '<p>CMS managed project.</p>',
            'is_active' => true,
        ]);

        $project->techStacks()->attach($techStack);

        ProjectCoreFeature::create([
            'project_id' => $project->id,
            'description' => 'Manage portfolio content from Filament.',
        ]);

        $notesCategory = NotesCategory::create([
            'title' => 'Engineering',
            'slug' => 'engineering',
            'is_active' => true,
            'order_no' => 1,
        ]);

        Note::create([
            'notes_category_id' => $notesCategory->id,
            'title' => 'CMS First',
            'slug' => 'cms-first',
            'is_active' => true,
        ]);

        ContactMessage::create([
            'name' => 'Visitor',
            'email' => 'visitor@example.com',
            'subject' => 'Hello',
            'message' => 'Nice portfolio.',
        ]);

        ContactInformation::create([
            'location' => 'Jakarta',
            'email' => 'hello@example.com',
        ]);

        $this->assertModelExists($project);
        $this->assertTrue($project->techStacks()->whereKey($techStack->id)->exists());
        $this->assertSame('Web Apps', $project->category->title);
        $this->assertSame('Engineering', Note::query()->firstOrFail()->category?->title);
    }
}
