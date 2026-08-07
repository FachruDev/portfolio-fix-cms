<?php

namespace App\Filament\Resources\Notes;

use App\Filament\Resources\Notes\Pages\ManageNotes;
use App\Models\Note;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class NoteResource extends Resource
{
    protected static ?string $model = Note::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Notes';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Select::make('notes_category_id')->relationship('category', 'title')->searchable()->preload(),
            TextInput::make('title')->required()->live(onBlur: true)->afterStateUpdated(fn (Set $set, ?string $state): mixed => $set('slug', Str::slug($state ?? '')))->maxLength(255),
            TextInput::make('slug')->required()->maxLength(255)->unique(ignoreRecord: true),
            TextInput::make('meta_title')->maxLength(255)->columnSpanFull(),
            Textarea::make('meta_description')->rows(3)->columnSpanFull(),
            FileUpload::make('og_image')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])->maxSize(2048)->disk(config('filesystems.media_disk', 'r2'))->directory('seo'),
            Toggle::make('is_active')->default(true)->required(),
            Toggle::make('is_featured')->default(false)->required(),
            DateTimePicker::make('published_at')->seconds(false),
            Textarea::make('subtitle')->rows(3)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
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
        ])->toolbarActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageNotes::route('/')];
    }
}
