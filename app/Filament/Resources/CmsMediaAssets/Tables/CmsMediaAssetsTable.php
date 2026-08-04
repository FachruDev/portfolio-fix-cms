<?php

namespace App\Filament\Resources\CmsMediaAssets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CmsMediaAssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('file_path')
                    ->label('Preview')
                    ->disk('public')
                    ->square(),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('collection')
                    ->badge()
                    ->sortable(),
                TextColumn::make('mime_type')
                    ->label('Type')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('formatted_size')
                    ->label('Size'),
                TextColumn::make('public_url')
                    ->label('URL')
                    ->copyable()
                    ->copyMessage('Media URL copied')
                    ->limit(40),
                IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('collection')
                    ->options([
                        'general' => 'General',
                        'logos' => 'Logos',
                        'seo' => 'SEO',
                        'projects' => 'Projects',
                        'about' => 'About',
                        'hobbies' => 'Hobbies',
                        'contact' => 'Contact',
                    ]),
                TernaryFilter::make('is_active'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
