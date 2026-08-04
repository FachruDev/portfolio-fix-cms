<?php

namespace App\Services;

use App\Http\Resources\PortfolioContentResource;
use App\Models\AboutPage;
use App\Models\AboutPageSection;
use App\Models\ContactInformation;
use App\Models\ExpertiseSection;
use App\Models\HobbySection;
use App\Models\LandingPage;
use App\Models\LandingPageAboutSection;
use App\Models\Note;
use App\Models\NotesCategory;
use App\Models\NotesHero;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectHero;
use App\Models\SiteSetting;
use App\Models\SocialMediaLink;
use App\Models\TechStack;
use App\Models\Tool;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class PortfolioContentService
{
    public const CacheKey = 'portfolio-content';

    /**
     * @return array<string, mixed>
     */
    public function get(): array
    {
        return Cache::remember(self::CacheKey, now()->addMinutes(15), fn (): array => $this->fresh());
    }

    public function resource(): PortfolioContentResource
    {
        return new PortfolioContentResource($this->get());
    }

    public function flush(): bool
    {
        return Cache::forget(self::CacheKey);
    }

    /**
     * @return array<string, mixed>
     */
    public function fresh(): array
    {
        return [
            'global' => [
                'site_settings' => $this->siteSettings(),
                'social_media_links' => SocialMediaLink::query()->active()->ordered()->get(),
                'tech_stacks' => TechStack::query()->active()->ordered()->get(),
            ],
            'landing_page' => [
                'hero' => LandingPage::query()->first(),
                'about_sections' => LandingPageAboutSection::query()->with('items')->ordered()->get(),
            ],
            'about_page' => [
                'hero' => AboutPage::query()->first(),
                'sections' => AboutPageSection::query()->with('items')->ordered()->get(),
                'expertise_sections' => ExpertiseSection::query()->ordered()->get(),
                'tools' => Tool::query()->active()->ordered()->get(),
                'hobby_sections' => HobbySection::query()->with('items')->ordered()->get(),
            ],
            'projects_page' => [
                'hero' => ProjectHero::query()->first(),
                'categories' => ProjectCategory::query()
                    ->active()
                    ->ordered()
                    ->with([
                        'projects' => fn ($query) => $query
                            ->with([
                                'techStacks' => fn ($query) => $query->active()->ordered(),
                                'coreFeatures',
                                'designHighlights',
                            ])
                            ->published()
                            ->latest('published_at')
                            ->latest('id'),
                    ])
                    ->get(),
                'featured_projects' => Project::query()
                    ->published()
                    ->where('is_featured', true)
                    ->with([
                        'category',
                        'techStacks' => fn ($query) => $query->active()->ordered(),
                        'coreFeatures',
                        'designHighlights',
                    ])
                    ->latest('published_at')
                    ->latest('id')
                    ->get(),
            ],
            'notes_page' => [
                'hero' => NotesHero::query()->first(),
                'categories' => NotesCategory::query()->active()->ordered()->with('notes')->get(),
                'featured_notes' => Note::query()
                    ->published()
                    ->where('is_featured', true)
                    ->with('category')
                    ->latest('published_at')
                    ->latest('id')
                    ->get(),
            ],
            'contact_page' => [
                'information' => ContactInformation::query()->first(),
            ],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function siteSettings(): ?array
    {
        $settings = SiteSetting::query()->first();

        if (! $settings) {
            return null;
        }

        return [
            ...$settings->toArray(),
            'logo_light_url' => $this->mediaUrl($settings->logo_light),
            'logo_dark_url' => $this->mediaUrl($settings->logo_dark),
            'favicon_url' => $this->mediaUrl($settings->favicon),
            'og_image_url' => $this->mediaUrl($settings->og_image),
        ];
    }

    private function mediaUrl(?string $path): ?string
    {
        return filled($path) ? Storage::disk('public')->url($path) : null;
    }
}
