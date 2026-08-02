<?php

namespace App\Filament\Resources\ExpertiseSections\Pages;

use App\Filament\Resources\ExpertiseSections\ExpertiseSectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageExpertiseSections extends ManageRecords
{
    protected static string $resource = ExpertiseSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
