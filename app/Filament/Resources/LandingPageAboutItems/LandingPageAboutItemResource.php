<?php

namespace App\Filament\Resources\LandingPageAboutItems;

use App\Filament\Resources\LandingPageAboutItems\Pages\ManageLandingPageAboutItems;
use App\Models\LandingPageAboutItem;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class LandingPageAboutItemResource extends Resource
{
    protected static ?string $model = LandingPageAboutItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Landing Page';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Select::make('landing_page_about_section_id')->relationship('section', 'title')->searchable()->preload()->required(),
            TextInput::make('title')->required()->maxLength(255),
            TextInput::make('icon')->maxLength(255),
            TextInput::make('order_no')->numeric()->default(0)->required(),
            Textarea::make('subtitle')->rows(3)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('section.title')->searchable()->sortable(),
            TextColumn::make('title')->searchable()->sortable(),
            TextColumn::make('icon')->searchable()->sortable(),
            TextColumn::make('order_no')->sortable(),
            TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])->defaultSort('order_no')->filters([])->recordActions([
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
        return ['index' => ManageLandingPageAboutItems::route('/')];
    }
}
