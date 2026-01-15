<?php

namespace App\Filament\GuestLetter\Resources\PostStayLetters\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;

class PostStayLettersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->where('type', 'post_stay')->with(['guestDirect', 'booking.guest']))
            ->columns([
                Tables\Columns\TextColumn::make('guestDirect.full_name')
                    ->label('Guest')
                    ->getStateUsing(function ($record) {
                        // Try guestDirect first (for manually created), then booking.guest
                        $guest = $record->guestDirect ?? $record->booking?->guest;
                        return $guest ? $guest->full_name : '-';
                    })
                    ->searchable(['guests.first_name', 'guests.last_name'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('to_email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'sent' => 'success',
                        'failed' => 'danger',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('scheduled_for')
                    ->label('Scheduled For')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sent_at')
                    ->label('Sent At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'sent' => 'Sent',
                        'failed' => 'Failed',
                    ]),
            ])
            ->recordActions([
                Action::make('send')
                    ->label('Send Now')
                    ->icon('heroicon-o-paper-airplane')
                    ->requiresConfirmation()
                    ->disabled(function ($record) {
                        return in_array($record->status, ['sent']);
                    })
                    ->action(function ($record) {
                        \App\Modules\GuestLetter\Jobs\SendGuestLetterJob::dispatch($record);

                        Notification::make()
                            ->title('Post-stay letter queued for sending')
                            ->success()
                            ->send();
                    }),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
