<?php

namespace App\Filament\Resources\AboutPageSections\Pages;

use App\Filament\Resources\AboutPageSections\AboutPageSectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageAboutPageSections extends ManageRecords
{
    protected static string $resource = AboutPageSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
