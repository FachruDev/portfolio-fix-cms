<?php

namespace App\Filament\Resources\NotesHeroes\Pages;

use App\Filament\Resources\NotesHeroes\NotesHeroResource;
use App\Models\NotesHero;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageNotesHeroes extends ManageRecords
{
    protected static string $resource = NotesHeroResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->hidden(fn (): bool => NotesHero::query()->exists())];
    }
}
