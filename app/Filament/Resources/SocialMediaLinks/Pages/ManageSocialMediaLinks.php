<?php

namespace App\Filament\Resources\SocialMediaLinks\Pages;

use App\Filament\Resources\SocialMediaLinks\SocialMediaLinkResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSocialMediaLinks extends ManageRecords
{
    protected static string $resource = SocialMediaLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
