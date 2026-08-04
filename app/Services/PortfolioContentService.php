<?php

namespace App\Services;

use App\Http\Resources\PortfolioContentResource;
use App\Models\AboutPage;
use App\Models\AboutPageSection;
use App\Models\CmsMediaAsset;
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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
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
                'site_settings' => $this->nullableRecord(SiteSetting::query()->first(), ['logo_light', 'logo_dark', 'favicon', 'og_image']),
                'social_media_links' => $this->records(SocialMediaLink::query()->active()->ordered()->get()),
                'tech_stacks' => $this->records(TechStack::query()->active()->ordered()->get(), ['icon']),
                'media_assets' => $this->records(CmsMediaAsset::query()->active()->latest()->get(), ['file_path']),
            ],
            'landing_page' => [
                'hero' => $this->nullableRecord(LandingPage::query()->first(), ['og_image']),
                'about_sections' => LandingPageAboutSection::query()
                    ->with('items')
                    ->ordered()
                    ->get()
                    ->map(fn (LandingPageAboutSection $section): array => [
                        ...$this->record($section),
                        'items' => $this->records($section->items, ['icon']),
                    ])
                    ->all(),
            ],
            'about_page' => [
                'hero' => $this->nullableRecord(AboutPage::query()->first(), ['photo', 'og_image']),
                'sections' => AboutPageSection::query()
                    ->with('items')
                    ->ordered()
                    ->get()
                    ->map(fn (AboutPageSection $section): array => [
                        ...$this->record($section),
                        'items' => $this->records($section->items, ['icon']),
                    ])
                    ->all(),
                'expertise_sections' => $this->records(ExpertiseSection::query()->ordered()->get()),
                'tools' => $this->records(Tool::query()->active()->ordered()->get(), ['logo']),
                'hobby_sections' => HobbySection::query()
                    ->with('items')
                    ->ordered()
                    ->get()
                    ->map(fn (HobbySection $section): array => [
                        ...$this->record($section),
                        'items' => $this->records($section->items, ['photo']),
                    ])
                    ->all(),
            ],
            'projects_page' => [
                'hero' => $this->nullableRecord(ProjectHero::query()->first(), ['og_image']),
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
                    ->get()
                    ->map(fn (ProjectCategory $category): array => [
                        ...$this->record($category),
                        'projects' => $category->projects
                            ->map(fn (Project $project): array => $this->project($project))
                            ->all(),
                    ])
                    ->all(),
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
                    ->get()
                    ->map(fn (Project $project): array => $this->project($project))
                    ->all(),
            ],
            'notes_page' => [
                'hero' => $this->nullableRecord(NotesHero::query()->first(), ['og_image']),
                'categories' => NotesCategory::query()
                    ->active()
                    ->ordered()
                    ->with('notes')
                    ->get()
                    ->map(fn (NotesCategory $category): array => [
                        ...$this->record($category),
                        'notes' => $category->notes
                            ->map(fn (Note $note): array => $this->note($note))
                            ->all(),
                    ])
                    ->all(),
                'featured_notes' => Note::query()
                    ->published()
                    ->where('is_featured', true)
                    ->with('category')
                    ->latest('published_at')
                    ->latest('id')
                    ->get()
                    ->map(fn (Note $note): array => $this->note($note))
                    ->all(),
            ],
            'contact_page' => [
                'information' => $this->nullableRecord(ContactInformation::query()->first(), ['image', 'og_image']),
            ],
        ];
    }

    /**
     * @param  Collection<int, Model>  $records
     * @param  array<int, string>  $mediaFields
     * @return array<int, array<string, mixed>>
     */
    private function records(Collection $records, array $mediaFields = []): array
    {
        return $records
            ->map(fn (Model $record): array => $this->record($record, $mediaFields))
            ->all();
    }

    /**
     * @param  array<int, string>  $mediaFields
     * @return array<string, mixed>|null
     */
    private function nullableRecord(?Model $record, array $mediaFields = []): ?array
    {
        if (! $record) {
            return null;
        }

        return $this->record($record, $mediaFields);
    }

    /**
     * @param  array<int, string>  $mediaFields
     * @return array<string, mixed>
     */
    private function record(Model $record, array $mediaFields = []): array
    {
        $data = $record->toArray();

        foreach ($mediaFields as $field) {
            $data[$field.'_url'] = $this->mediaUrl(data_get($data, $field));
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function project(Project $project): array
    {
        $data = $this->record($project, ['thumbnail', 'og_image']);

        if ($project->relationLoaded('category')) {
            $data['category'] = $project->category ? $this->record($project->category) : null;
        }

        if ($project->relationLoaded('techStacks')) {
            $data['tech_stacks'] = $this->records($project->techStacks, ['icon']);
        }

        if ($project->relationLoaded('coreFeatures')) {
            $data['core_features'] = $this->records($project->coreFeatures);
        }

        if ($project->relationLoaded('designHighlights')) {
            $data['design_highlights'] = $this->records($project->designHighlights, ['photo']);
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function note(Note $note): array
    {
        $data = $this->record($note, ['og_image']);

        if ($note->relationLoaded('category')) {
            $data['category'] = $note->category ? $this->record($note->category) : null;
        }

        return $data;
    }

    private function mediaUrl(?string $path): ?string
    {
        return filled($path) ? Storage::disk('public')->url($path) : null;
    }
}
