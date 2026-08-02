<?php

namespace App\Filament\Resources\ProjectDesignHighlights;

use App\Filament\Resources\ProjectDesignHighlights\Pages\ManageProjectDesignHighlights;
use App\Models\ProjectDesignHighlight;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ProjectDesignHighlightResource extends Resource
{
    protected static ?string $model = ProjectDesignHighlight::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Projects';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Select::make('project_id')->relationship('project', 'title')->searchable()->preload()->required(),
            TextInput::make('title')->required()->maxLength(255),
            FileUpload::make('photo')->image()->disk('public')->directory('projects/design-highlights'),
            Textarea::make('subtitle')->rows(3)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('photo')->disk('public'),
            TextColumn::make('project.title')->searchable()->sortable(),
            TextColumn::make('title')->searchable()->sortable(),
            TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])->filters([])->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ])->toolbarActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageProjectDesignHighlights::route('/')];
    }
}
