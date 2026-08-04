<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Projects\ProjectResource;
use App\Models\AboutPage;
use App\Models\LandingPage;
use App\Models\Note;
use App\Models\Project;
use App\Models\ProjectHero;
use App\Models\SiteSetting;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContentReadiness extends StatsOverviewWidget
{
    protected ?string $heading = 'Publishing Readiness';

    protected ?string $description = 'Hal yang perlu dicek sebelum konten dipakai frontend.';

    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected int|array|null $columns = [
        'default' => 1,
        'md' => 3,
    ];

    public static function canView(): bool
    {
        return ProjectResource::canViewAny();
    }

    protected function getStats(): array
    {
        $missingGlobalSeo = collect([
            SiteSetting::query()->first(['meta_title', 'meta_description']),
            LandingPage::query()->first(['meta_title', 'meta_description']),
            AboutPage::query()->first(['meta_title', 'meta_description']),
            ProjectHero::query()->first(['meta_title', 'meta_description']),
        ])->filter(fn ($record): bool => ! $record || blank($record->meta_title) || blank($record->meta_description))
            ->count();

        $projectNeedsReview = Project::query()
            ->where('is_active', true)
            ->where(fn ($query) => $query
                ->whereNull('published_at')
                ->orWhere('published_at', '>', now())
                ->orWhereNull('thumbnail')
                ->orWhereNull('meta_title')
                ->orWhereNull('meta_description'))
            ->count();

        $noteNeedsReview = Note::query()
            ->where('is_active', true)
            ->where(fn ($query) => $query
                ->whereNull('published_at')
                ->orWhere('published_at', '>', now())
                ->orWhereNull('meta_title')
                ->orWhereNull('meta_description'))
            ->count();

        return [
            Stat::make('Global SEO Gaps', number_format($missingGlobalSeo))
                ->description('Site, landing, about, project hero')
                ->descriptionIcon($missingGlobalSeo > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($missingGlobalSeo > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-magnifying-glass'),
            Stat::make('Project Review', number_format($projectNeedsReview))
                ->description('Missing publish date, thumbnail, or SEO')
                ->descriptionIcon($projectNeedsReview > 0 ? 'heroicon-m-clock' : 'heroicon-m-check-circle')
                ->color($projectNeedsReview > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-briefcase'),
            Stat::make('Notes Review', number_format($noteNeedsReview))
                ->description('Missing publish date or SEO')
                ->descriptionIcon($noteNeedsReview > 0 ? 'heroicon-m-clock' : 'heroicon-m-check-circle')
                ->color($noteNeedsReview > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-document-text'),
        ];
    }
}
