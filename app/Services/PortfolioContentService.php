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
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class PortfolioContentService
{
    public const CacheKey = 'portfolio-content:bootstrap';

    public const CacheVersionKey = 'portfolio-content:version';

    private const CacheTtlMinutes = 15;

    /**
     * @return array<string, mixed>
     */
    public function get(): array
    {
        return $this->bootstrap();
    }

    public function resource(): PortfolioContentResource
    {
        return new PortfolioContentResource($this->get());
    }

    public function flush(): bool
    {
        Cache::add(self::CacheVersionKey, 1);
        Cache::increment(self::CacheVersionKey);

        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function bootstrap(): array
    {
        return $this->cached('bootstrap', fn (): array => [
            'global' => $this->global(),
            'landing_page' => [
                'hero' => $this->nullableRecord(LandingPage::query()->first(), ['og_image']),
            ],
            'projects_page' => [
                'hero' => $this->nullableRecord(ProjectHero::query()->first(), ['og_image']),
                'featured_projects' => Project::query()
                    ->published()
                    ->where('is_featured', true)
                    ->with([
                        'category',
                        'techStacks' => fn ($query) => $query->active()->ordered(),
                    ])
                    ->latest('published_at')
                    ->latest('id')
                    ->limit(6)
                    ->get()
                    ->map(fn (Project $project): array => $this->projectSummary($project))
                    ->all(),
            ],
            'notes_page' => [
                'hero' => $this->nullableRecord(NotesHero::query()->first(), ['og_image']),
                'featured_notes' => Note::query()
                    ->published()
                    ->where('is_featured', true)
                    ->with('category')
                    ->latest('published_at')
                    ->latest('id')
                    ->limit(6)
                    ->get()
                    ->map(fn (Note $note): array => $this->noteRecord($note))
                    ->all(),
            ],
            'contact_page' => [
                'information' => $this->contactInformation(),
            ],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function global(): array
    {
        return $this->cached('global', fn (): array => [
            'site_settings' => $this->nullableRecord(SiteSetting::query()->first(), ['logo_light', 'logo_dark', 'favicon', 'og_image']),
            'social_media_links' => $this->records(SocialMediaLink::query()->active()->ordered()->get()),
            'tech_stacks' => $this->records(TechStack::query()->active()->ordered()->get(), ['icon']),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function landingPage(): array
    {
        return $this->cached('landing-page', fn (): array => [
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
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function aboutPage(): array
    {
        return $this->cached('about-page', fn (): array => [
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
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function projectsPage(): array
    {
        return $this->cached('projects-page', fn (): array => [
            'hero' => $this->nullableRecord(ProjectHero::query()->first(), ['og_image']),
            'categories' => $this->projectCategories(),
            'featured_projects' => Project::query()
                ->published()
                ->where('is_featured', true)
                ->with([
                    'category',
                    'techStacks' => fn ($query) => $query->active()->ordered(),
                ])
                ->latest('published_at')
                ->latest('id')
                ->limit(6)
                ->get()
                ->map(fn (Project $project): array => $this->projectSummary($project))
                ->all(),
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function projectCategories(): array
    {
        return $this->cached('project-categories', fn (): array => $this->records(
            ProjectCategory::query()->active()->ordered()->get(),
        ));
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function projects(array $filters = []): array
    {
        return $this->cached('projects', function () use ($filters): array {
            $query = Project::query()
                ->published()
                ->with([
                    'category',
                    'techStacks' => fn ($query) => $query->active()->ordered(),
                ])
                ->when($filters['category'] ?? null, fn ($query, string $category): mixed => $query
                    ->whereHas('category', fn ($query): mixed => $query->active()->where('slug', $category)))
                ->when(array_key_exists('featured', $filters), fn ($query): mixed => $query
                    ->where('is_featured', (bool) $filters['featured']))
                ->latest('published_at')
                ->latest('id');

            $paginator = $query->paginate(
                perPage: (int) ($filters['per_page'] ?? 9),
                page: (int) ($filters['page'] ?? 1),
            );

            return $this->paginated($paginator, fn (Project $project): array => $this->projectSummary($project));
        }, $filters);
    }

    /**
     * @return array<string, mixed>
     */
    public function project(string $slug): array
    {
        return $this->cached('project', function () use ($slug): array {
            $project = Project::query()
                ->published()
                ->where('slug', $slug)
                ->with([
                    'category',
                    'techStacks' => fn ($query) => $query->active()->ordered(),
                    'coreFeatures',
                    'designHighlights',
                ])
                ->firstOrFail();

            return $this->projectDetail($project);
        }, ['slug' => $slug]);
    }

    /**
     * @return array<string, mixed>
     */
    public function notesPage(): array
    {
        return $this->cached('notes-page', fn (): array => [
            'hero' => $this->nullableRecord(NotesHero::query()->first(), ['og_image']),
            'categories' => $this->notesCategories(),
            'featured_notes' => Note::query()
                ->published()
                ->where('is_featured', true)
                ->with('category')
                ->latest('published_at')
                ->latest('id')
                ->limit(6)
                ->get()
                ->map(fn (Note $note): array => $this->noteRecord($note))
                ->all(),
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function notesCategories(): array
    {
        return $this->cached('notes-categories', fn (): array => $this->records(
            NotesCategory::query()->active()->ordered()->get(),
        ));
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function notes(array $filters = []): array
    {
        return $this->cached('notes', function () use ($filters): array {
            $query = Note::query()
                ->published()
                ->with('category')
                ->when($filters['category'] ?? null, fn ($query, string $category): mixed => $query
                    ->whereHas('category', fn ($query): mixed => $query->active()->where('slug', $category)))
                ->when(array_key_exists('featured', $filters), fn ($query): mixed => $query
                    ->where('is_featured', (bool) $filters['featured']))
                ->latest('published_at')
                ->latest('id');

            $paginator = $query->paginate(
                perPage: (int) ($filters['per_page'] ?? 10),
                page: (int) ($filters['page'] ?? 1),
            );

            return $this->paginated($paginator, fn (Note $note): array => $this->noteRecord($note));
        }, $filters);
    }

    /**
     * @return array<string, mixed>
     */
    public function note(string $slug): array
    {
        return $this->cached('note', function () use ($slug): array {
            $note = Note::query()
                ->published()
                ->where('slug', $slug)
                ->with('category')
                ->firstOrFail();

            return $this->noteRecord($note);
        }, ['slug' => $slug]);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function contactInformation(): ?array
    {
        return $this->cached('contact-information', fn (): ?array => $this->nullableRecord(
            ContactInformation::query()->first(),
            ['image', 'og_image'],
        ));
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function mediaAssets(array $filters = []): array
    {
        return $this->cached('media-assets', function () use ($filters): array {
            $query = CmsMediaAsset::query()
                ->active()
                ->when($filters['collection'] ?? null, fn ($query, string $collection): mixed => $query->where('collection', $collection))
                ->latest();

            $paginator = $query->paginate(
                perPage: (int) ($filters['per_page'] ?? 24),
                page: (int) ($filters['page'] ?? 1),
            );

            return $this->paginated($paginator, fn (CmsMediaAsset $asset): array => $this->record($asset, ['file_path']));
        }, $filters);
    }

    /**
     * @return array<string, mixed>
     */
    public function fresh(): array
    {
        return [
            'global' => [
                ...$this->global(),
                'media_assets' => $this->mediaAssets(['per_page' => 24])['data'],
            ],
            'landing_page' => $this->landingPage(),
            'about_page' => $this->aboutPage(),
            'projects_page' => $this->projectsPage(),
            'notes_page' => $this->notesPage(),
            'contact_page' => [
                'information' => $this->contactInformation(),
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
    private function projectSummary(Project $project): array
    {
        $data = $this->record($project, ['thumbnail', 'og_image']);

        unset($data['description_rich']);

        if ($project->relationLoaded('category')) {
            $data['category'] = $project->category ? $this->record($project->category) : null;
        }

        if ($project->relationLoaded('techStacks')) {
            $data['tech_stacks'] = $this->records($project->techStacks, ['icon']);
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function projectDetail(Project $project): array
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
    private function noteRecord(Note $note): array
    {
        $data = $this->record($note, ['og_image']);

        if ($note->relationLoaded('category')) {
            $data['category'] = $note->category ? $this->record($note->category) : null;
        }

        return $data;
    }

    private function mediaUrl(?string $path): ?string
    {
        return filled($path) ? Storage::disk($this->mediaDisk())->url($path) : null;
    }

    private function mediaDisk(): string
    {
        return (string) config('filesystems.media_disk', 'r2');
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    private function cached(string $key, Closure $callback, array $parameters = []): mixed
    {
        $version = Cache::get(self::CacheVersionKey, 1);
        $parametersHash = md5(json_encode($parameters));

        return Cache::remember(
            "portfolio-content:v{$version}:{$key}:{$parametersHash}",
            now()->addMinutes(self::CacheTtlMinutes),
            $callback,
        );
    }

    /**
     * @template TModel of Model
     *
     * @param  LengthAwarePaginator<int, TModel>  $paginator
     * @param  Closure(TModel): array<string, mixed>  $mapper
     * @return array<string, mixed>
     */
    private function paginated(LengthAwarePaginator $paginator, Closure $mapper): array
    {
        return [
            'data' => $paginator->getCollection()
                ->map(fn (Model $record): array => $mapper($record))
                ->values()
                ->all(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'from' => $paginator->firstItem(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ];
    }
}
