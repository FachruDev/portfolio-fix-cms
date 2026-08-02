<?php

namespace App\Filament\Resources\ContactInformation\Pages;

use App\Filament\Resources\ContactInformation\ContactInformationResource;
use App\Models\ContactInformation;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageContactInformation extends ManageRecords
{
    protected static string $resource = ContactInformationResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->hidden(fn (): bool => ContactInformation::query()->exists())];
    }
}
