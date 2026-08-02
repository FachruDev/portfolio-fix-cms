<?php

namespace App\Filament\Resources\ProjectHeroes\Pages;

use App\Filament\Resources\ProjectHeroes\ProjectHeroResource;
use App\Models\ProjectHero;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageProjectHeroes extends ManageRecords
{
    protected static string $resource = ProjectHeroResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->hidden(fn (): bool => ProjectHero::query()->exists())];
    }
}
