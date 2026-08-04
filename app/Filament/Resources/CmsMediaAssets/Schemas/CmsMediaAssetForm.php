<?php

namespace App\Filament\Resources\CmsMediaAssets\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CmsMediaAssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Select::make('collection')
                    ->options([
                        'general' => 'General',
                        'logos' => 'Logos',
                        'seo' => 'SEO',
                        'projects' => 'Projects',
                        'about' => 'About',
                        'hobbies' => 'Hobbies',
                        'contact' => 'Contact',
                    ])
                    ->default('general')
                    ->required()
                    ->searchable(),
                FileUpload::make('file_path')
                    ->label('File')
                    ->required()
                    ->disk('public')
                    ->directory(fn (Get $get): string => 'media-library/'.Str::slug($get('collection') ?: 'general'))
                    ->acceptedFileTypes([
                        'image/jpeg',
                        'image/png',
                        'image/webp',
                        'image/svg+xml',
                        'application/pdf',
                    ])
                    ->maxSize(5120)
                    ->downloadable()
                    ->openable()
                    ->columnSpanFull(),
                TextInput::make('alt_text')
                    ->maxLength(255),
                Toggle::make('is_active')
                    ->default(true)
                    ->required(),
                Textarea::make('caption')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
