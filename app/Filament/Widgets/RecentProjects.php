<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Projects\ProjectResource;
use App\Models\Project;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentProjects extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

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
            ->query(fn (): Builder => Project::query()->with('category')->latest())
            ->columns([
                ImageColumn::make('thumbnail')
                    ->disk('public')
                    ->label('Cover'),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(35),
                TextColumn::make('category.title')
                    ->label('Category')
                    ->sortable(),
                IconColumn::make('is_featured')
                    ->boolean()
                    ->label('Featured'),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                TextColumn::make('published_at')
                    ->dateTime()
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
