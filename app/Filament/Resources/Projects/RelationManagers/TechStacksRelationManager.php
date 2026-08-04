<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Services\PortfolioContentService;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TechStacksRelationManager extends RelationManager
{
    protected static string $relationship = 'techStacks';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('icon')
                    ->toggleable(),
                TextColumn::make('order_no')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->defaultSort('order_no')
            ->headerActions([
                AttachAction::make()
                    ->after(fn (): mixed => app(PortfolioContentService::class)->flush()),
            ])
            ->recordActions([
                DetachAction::make()
                    ->after(fn (): mixed => app(PortfolioContentService::class)->flush()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make()
                        ->after(fn (): mixed => app(PortfolioContentService::class)->flush()),
                ]),
            ]);
    }
}
