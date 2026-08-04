<?php

namespace App\Filament\Widgets;

use App\Models\CmsMediaAsset;
use App\Models\ContactMessage;
use App\Models\Note;
use App\Models\Project;
use App\Models\TechStack;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PortfolioOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Portfolio CMS Overview';

    protected ?string $description = 'Ringkasan utama untuk konten portfolio.';

    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected int|array|null $columns = [
        'default' => 1,
        'md' => 2,
        'xl' => 4,
    ];

    protected function getStats(): array
    {
        $publishedProjectCount = Project::query()->published()->count();
        $publishedNoteCount = Note::query()->published()->count();
        $newMessageCount = ContactMessage::query()
            ->where('status', ContactMessage::StatusNew)
            ->count();
        $mediaAssetCount = CmsMediaAsset::query()->active()->count();

        return [
            Stat::make('Projects', number_format($publishedProjectCount))
                ->description(Project::query()->where('is_featured', true)->published()->count().' featured')
                ->descriptionIcon('heroicon-m-star')
                ->color('success')
                ->icon('heroicon-o-briefcase'),
            Stat::make('Notes', number_format($publishedNoteCount))
                ->description(Note::query()->where('is_featured', true)->published()->count().' featured')
                ->descriptionIcon('heroicon-m-document-check')
                ->color('info')
                ->icon('heroicon-o-document-text'),
            Stat::make('Inbox', number_format($newMessageCount))
                ->description($newMessageCount > 0 ? 'Needs response' : 'Inbox clear')
                ->descriptionIcon($newMessageCount > 0 ? 'heroicon-m-bell-alert' : 'heroicon-m-check-circle')
                ->color($newMessageCount > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-envelope'),
            Stat::make('Media', number_format($mediaAssetCount))
                ->description(TechStack::query()->active()->count().' active tech stacks')
                ->descriptionIcon('heroicon-m-photo')
                ->color('gray')
                ->icon('heroicon-o-photo'),
        ];
    }
}
