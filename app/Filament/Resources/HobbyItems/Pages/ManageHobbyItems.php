<?php

namespace App\Filament\Resources\HobbyItems\Pages;

use App\Filament\Resources\HobbyItems\HobbyItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageHobbyItems extends ManageRecords
{
    protected static string $resource = HobbyItemResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
