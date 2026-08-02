<?php

namespace App\Filament\Resources\HobbySections\Pages;

use App\Filament\Resources\HobbySections\HobbySectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageHobbySections extends ManageRecords
{
    protected static string $resource = HobbySectionResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
