<?php

namespace App\Filament\Resources\CmsMediaAssets\Pages;

use App\Filament\Resources\CmsMediaAssets\CmsMediaAssetResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCmsMediaAsset extends EditRecord
{
    protected static string $resource = CmsMediaAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
