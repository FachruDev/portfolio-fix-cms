<?php

namespace App\Filament\Resources\CmsMediaAssets;

use App\Filament\Resources\CmsMediaAssets\Pages\CreateCmsMediaAsset;
use App\Filament\Resources\CmsMediaAssets\Pages\EditCmsMediaAsset;
use App\Filament\Resources\CmsMediaAssets\Pages\ListCmsMediaAssets;
use App\Filament\Resources\CmsMediaAssets\Schemas\CmsMediaAssetForm;
use App\Filament\Resources\CmsMediaAssets\Tables\CmsMediaAssetsTable;
use App\Models\CmsMediaAsset;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CmsMediaAssetResource extends Resource
{
    protected static ?string $model = CmsMediaAsset::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|UnitEnum|null $navigationGroup = 'Media';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return CmsMediaAssetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CmsMediaAssetsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCmsMediaAssets::route('/'),
            'create' => CreateCmsMediaAsset::route('/create'),
            'edit' => EditCmsMediaAsset::route('/{record}/edit'),
        ];
    }
}
