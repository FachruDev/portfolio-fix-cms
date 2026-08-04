<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Projects\ProjectResource;
use App\Models\Project;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentProjects extends TableWidget
{
    protected int|string|array $columnSpan = [
        'default' => 'full',
        'xl' => 2,
    ];

    protected static ?int $sort = 3;

    protected static bool $isLazy = false;

    public static function canView(): bool
    {
        return ProjectResource::canViewAny();
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Recent Projects')
            ->description('Project terbaru yang sedang dikelola.')
            ->paginated([5])
            ->defaultPaginationPageOption(5)
            ->query(fn (): Builder => Project::query()->with('category')->latest())
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(35),
                TextColumn::make('category.title')
                    ->label('Category')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                TextColumn::make('published_at')
                    ->dateTime()
                    ->since()
                    ->sortable(),
            ])
            ->headerActions([
                Action::make('createProject')
                    ->label('New project')
                    ->icon('heroicon-m-plus')
                    ->url(ProjectResource::getUrl('create'))
                    ->visible(fn (): bool => ProjectResource::canCreate()),
                Action::make('manageProjects')
                    ->label('Manage all')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(ProjectResource::getUrl()),
            ])
            ->recordActions([
                EditAction::make()
                    ->url(fn (Project $record): string => ProjectResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
