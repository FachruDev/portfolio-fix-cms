<?php

namespace App\Filament\Resources\CmsMediaAssets\Pages;

use App\Filament\Resources\CmsMediaAssets\CmsMediaAssetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCmsMediaAssets extends ListRecords
{
    protected static string $resource = CmsMediaAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
