<?php

namespace App\Filament\Resources\LandingPageAboutSections\Pages;

use App\Filament\Resources\LandingPageAboutSections\LandingPageAboutSectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageLandingPageAboutSections extends ManageRecords
{
    protected static string $resource = LandingPageAboutSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
