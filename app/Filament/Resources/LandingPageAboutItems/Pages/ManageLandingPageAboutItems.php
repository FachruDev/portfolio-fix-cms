<?php

namespace App\Filament\Resources\LandingPageAboutItems\Pages;

use App\Filament\Resources\LandingPageAboutItems\LandingPageAboutItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageLandingPageAboutItems extends ManageRecords
{
    protected static string $resource = LandingPageAboutItemResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
