<?php

namespace App\Filament\Resources\HobbyItems;

use App\Filament\Resources\HobbyItems\Pages\ManageHobbyItems;
use App\Models\HobbyItem;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class HobbyItemResource extends Resource
{
    protected static ?string $model = HobbyItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'About Page';

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Select::make('hobby_section_id')->relationship('section', 'title')->searchable()->preload()->required(),
            FileUpload::make('photo')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])->maxSize(2048)->disk(config('filesystems.media_disk', 'r2'))->directory('hobbies')->required(),
            TextInput::make('photo_alt')->maxLength(255),
            TextInput::make('order_no')->numeric()->default(0)->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('photo')->disk(config('filesystems.media_disk', 'r2')),
            TextColumn::make('section.title')->searchable()->sortable(),
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
        return ['index' => ManageHobbyItems::route('/')];
    }
}
