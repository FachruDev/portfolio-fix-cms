<?php

namespace App\Filament\Resources\ProjectDesignHighlights\Pages;

use App\Filament\Resources\ProjectDesignHighlights\ProjectDesignHighlightResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageProjectDesignHighlights extends ManageRecords
{
    protected static string $resource = ProjectDesignHighlightResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
