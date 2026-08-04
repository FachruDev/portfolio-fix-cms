<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestContactMessages extends TableWidget
{
    protected int|string|array $columnSpan = [
        'default' => 'full',
        'xl' => 2,
    ];

    protected static ?int $sort = 4;

    protected static bool $isLazy = false;

    public static function canView(): bool
    {
        return ContactMessageResource::canViewAny();
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Latest Contact Messages')
            ->description('Inbox terbaru dari form portfolio.')
            ->paginated([5])
            ->defaultPaginationPageOption(5)
            ->query(fn (): Builder => ContactMessage::query()->latest())
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subject')
                    ->searchable()
                    ->limit(45),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        ContactMessage::StatusNew => 'danger',
                        ContactMessage::StatusRead => 'warning',
                        ContactMessage::StatusReplied => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->since()
                    ->sortable(),
            ])
            ->headerActions([
                Action::make('openInbox')
                    ->label('Open inbox')
                    ->icon('heroicon-m-inbox')
                    ->url(ContactMessageResource::getUrl()),
            ])
            ->recordActions([
                Action::make('markAsRead')
                    ->label('Read')
                    ->icon('heroicon-m-eye')
                    ->authorize('update')
                    ->visible(fn (ContactMessage $record): bool => $record->status !== ContactMessage::StatusRead)
                    ->action(fn (ContactMessage $record): mixed => $record->markAsRead()),
                Action::make('markAsReplied')
                    ->label('Replied')
                    ->icon('heroicon-m-check-circle')
                    ->authorize('update')
                    ->visible(fn (ContactMessage $record): bool => $record->status !== ContactMessage::StatusReplied)
                    ->action(fn (ContactMessage $record): mixed => $record->markAsReplied()),
                DeleteAction::make(),
            ]);
    }
}
