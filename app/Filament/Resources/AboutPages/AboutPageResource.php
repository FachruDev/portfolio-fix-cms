<?php

namespace App\Filament\Resources\AboutPages;

use App\Filament\Resources\AboutPages\Pages\ManageAboutPages;
use App\Models\AboutPage;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class AboutPageResource extends Resource
{
    protected static ?string $model = AboutPage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'About Page';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('title')->required()->maxLength(255),
            FileUpload::make('photo')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])->maxSize(2048)->disk(config('filesystems.media_disk', 'r2'))->directory('about-page'),
            TextInput::make('photo_alt')->maxLength(255),
            Textarea::make('subtitle')->rows(3)->columnSpanFull(),
            TextInput::make('meta_title')->maxLength(255)->columnSpanFull(),
            Textarea::make('meta_description')->rows(3)->columnSpanFull(),
            FileUpload::make('og_image')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])->maxSize(2048)->disk(config('filesystems.media_disk', 'r2'))->directory('seo'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('photo')->disk(config('filesystems.media_disk', 'r2')),
            TextColumn::make('title')->searchable()->sortable(),
            TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])->filters([])->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ])->toolbarActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function canCreate(): bool
    {
        return parent::canCreate() && AboutPage::query()->doesntExist();
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return ['index' => ManageAboutPages::route('/')];
    }
}
