<?php

namespace App\Filament\Resources\SiteSettings;

use App\Filament\Resources\SiteSettings\Pages\ManageSiteSettings;
use App\Models\SiteSetting;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class SiteSettingResource extends Resource
{
    protected static ?string $model = SiteSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Global';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('site_title')->required()->maxLength(255),
            FileUpload::make('logo_light')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])->maxSize(2048)->disk(config('filesystems.media_disk', 'r2'))->directory('site-settings'),
            TextInput::make('logo_light_alt')->maxLength(255),
            FileUpload::make('logo_dark')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])->maxSize(2048)->disk(config('filesystems.media_disk', 'r2'))->directory('site-settings'),
            TextInput::make('logo_dark_alt')->maxLength(255),
            FileUpload::make('favicon')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])->maxSize(2048)->disk(config('filesystems.media_disk', 'r2'))->directory('site-settings'),
            TextInput::make('copyright_text')->maxLength(255),
            TextInput::make('meta_title')->maxLength(255)->columnSpanFull(),
            Textarea::make('meta_description')->rows(3)->columnSpanFull(),
            FileUpload::make('og_image')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])->maxSize(2048)->disk(config('filesystems.media_disk', 'r2'))->directory('seo'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('site_title')->searchable()->sortable(),
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
        return parent::canCreate() && SiteSetting::query()->doesntExist();
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
        return ['index' => ManageSiteSettings::route('/')];
    }
}
