<?php

namespace App\Filament\Resources\AboutPageSectionItems\Pages;

use App\Filament\Resources\AboutPageSectionItems\AboutPageSectionItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageAboutPageSectionItems extends ManageRecords
{
    protected static string $resource = AboutPageSectionItemResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
