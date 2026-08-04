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

    public function test_portfolio_cms_records_can_be_created_with_best_practice_fields(): void
    {
        $techStack = TechStack::create([
            'name' => 'Laravel',
            'icon' => 'code',
            'is_active' => true,
            'order_no' => 1,
        ]);

        SiteSetting::create([
            'site_title' => 'Portfolio CMS',
            'copyright_text' => 'Copyright 2026',
            'meta_title' => 'Portfolio',
            'meta_description' => 'Personal portfolio website.',
        ]);

        LandingPage::create([
            'title' => 'Build better products',
            'subtitle' => 'Selected projects and notes.',
            'meta_title' => 'Home',
        ]);

        AboutPage::create([
            'title' => 'About Me',
            'subtitle' => 'Full-stack developer.',
            'photo_alt' => 'Profile photo',
        ]);

        ProjectHero::create([
            'title' => 'Projects',
            'subtitle' => 'Selected work.',
            'meta_title' => 'Projects',
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
            'thumbnail_alt' => 'Portfolio API thumbnail',
            'is_active' => true,
            'is_featured' => true,
            'published_at' => now(),
            'meta_title' => 'Portfolio API',
            'meta_description' => 'Read-only portfolio content source.',
        ]);

        $project->techStacks()->attach($techStack);

        ProjectCoreFeature::create([
            'project_id' => $project->id,
            'description' => 'Manage portfolio content from Filament.',
            'order_no' => 1,
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
            'is_featured' => true,
            'published_at' => now(),
            'meta_title' => 'CMS First',
        ]);

        $message = ContactMessage::create([
            'name' => 'Visitor',
            'email' => 'visitor@example.com',
            'subject' => 'Hello',
            'message' => 'Nice portfolio.',
        ]);

        ContactInformation::create([
            'location' => 'Jakarta',
            'email' => 'hello@example.com',
            'image_alt' => 'Contact illustration',
        ]);

        $this->assertModelExists($project);
        $this->assertTrue($project->techStacks()->whereKey($techStack->id)->exists());
        $this->assertSame('Web Apps', $project->category->title);
        $this->assertSame('Engineering', Note::query()->firstOrFail()->category?->title);
        $this->assertSame(ContactMessage::StatusNew, $message->status);
        $this->assertTrue(Project::query()->published()->whereKey($project->id)->exists());
    }
}
