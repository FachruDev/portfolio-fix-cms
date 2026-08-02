<?php

namespace App\Filament\Resources\ProjectCoreFeatures\Pages;

use App\Filament\Resources\ProjectCoreFeatures\ProjectCoreFeatureResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageProjectCoreFeatures extends ManageRecords
{
    protected static string $resource = ProjectCoreFeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
