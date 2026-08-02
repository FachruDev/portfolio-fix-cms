<?php

namespace App\Filament\Resources\NotesCategories\Pages;

use App\Filament\Resources\NotesCategories\NotesCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageNotesCategories extends ManageRecords
{
    protected static string $resource = NotesCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
