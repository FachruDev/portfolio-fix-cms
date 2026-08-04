<?php

namespace App\Filament\Resources\Projects;

use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Filament\Resources\Projects\RelationManagers\CoreFeaturesRelationManager;
use App\Filament\Resources\Projects\RelationManagers\DesignHighlightsRelationManager;
use App\Filament\Resources\Projects\RelationManagers\TechStacksRelationManager;
use App\Models\Project;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Projects';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Select::make('project_category_id')->relationship('category', 'title')->searchable()->preload()->required(),
            TextInput::make('title')->required()->live(onBlur: true)->afterStateUpdated(fn (Set $set, ?string $state): mixed => $set('slug', Str::slug($state ?? '')))->maxLength(255),
            TextInput::make('slug')->required()->maxLength(255)->unique(ignoreRecord: true),
            FileUpload::make('thumbnail')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])->maxSize(2048)->disk('public')->directory('projects/thumbnails'),
            TextInput::make('thumbnail_alt')->maxLength(255),
            Toggle::make('is_active')->default(true)->required(),
            Toggle::make('is_featured')->default(false)->required(),
            DateTimePicker::make('published_at')->seconds(false),
            TextInput::make('github_url')->url()->maxLength(255),
            TextInput::make('live_url')->url()->maxLength(255),
            Textarea::make('subtitle')->rows(3)->columnSpanFull(),
            RichEditor::make('description_rich')->columnSpanFull(),
            TextInput::make('meta_title')->maxLength(255)->columnSpanFull(),
            Textarea::make('meta_description')->rows(3)->columnSpanFull(),
            FileUpload::make('og_image')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])->maxSize(2048)->disk('public')->directory('seo'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('thumbnail')->disk('public'),
            TextColumn::make('category.title')->searchable()->sortable(),
            TextColumn::make('title')->searchable()->sortable(),
            TextColumn::make('slug')->searchable()->sortable(),
            IconColumn::make('is_featured')->boolean()->sortable(),
            IconColumn::make('is_active')->boolean()->sortable(),
            TextColumn::make('published_at')->dateTime()->sortable(),
            TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])->filters([
            TernaryFilter::make('is_active'),
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            TechStacksRelationManager::class,
            CoreFeaturesRelationManager::class,
            DesignHighlightsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'edit' => EditProject::route('/{record}/edit'),
        ];
    }
}
