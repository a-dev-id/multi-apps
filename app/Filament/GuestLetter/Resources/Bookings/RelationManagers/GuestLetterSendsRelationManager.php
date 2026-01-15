<?php

namespace App\Filament\GuestLetter\Resources\Bookings\RelationManagers;

use App\Modules\GuestLetter\Jobs\SendGuestLetterJob;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GuestLetterSendsRelationManager extends RelationManager
{
    protected static string $relationship = 'guestLetterSends';

    protected static ?string $title = 'Guest Letters';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->orderByRaw("
                FIELD(type, 'confirmation','pre_arrival','post_stay')
            "))
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'confirmation' => 'Confirmation',
                        'pre_arrival'  => 'Pre-arrival',
                        'post_stay'    => 'Post-stay',
                        default        => $state,
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn(?string $state) => ucfirst($state ?? '-'))
                    ->color(fn(?string $state) => match ($state) {
                        'sent'      => 'success',
                        'pending'   => 'warning',
                        'failed'    => 'danger',
                        'cancelled' => 'gray',
                        default     => 'gray',
                    }),

                Tables\Columns\TextColumn::make('to_email')
                    ->label('To')
                    ->limit(30)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('scheduled_for')
                    ->label('Scheduled')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('sent_at')
                    ->label('Sent')
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('failed_at')
                    ->label('Failed')
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('error_message')
                    ->label('Error')
                    ->wrap()
                    ->limit(60)
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            // Filament v4 uses recordActions() for row actions (not actions()).
            ->recordActions([
                Action::make('sendNow')
                    ->label('Send now')
                    ->icon('heroicon-m-paper-airplane')
                    ->requiresConfirmation()
                    ->visible(fn($record) => in_array($record->status, ['pending', 'failed'], true))
                    ->action(function ($record) {
                        $record->update([
                            'status'        => 'pending',
                            'scheduled_for' => null,
                            'sent_at'       => null,
                            'failed_at'     => null,
                            'error_message' => null,
                        ]);

                        SendGuestLetterJob::dispatchSync($record->id);

                        Notification::make()
                            ->title('Queued to send')
                            ->success()
                            ->send();
                    }),

                Action::make('retry')
                    ->label('Retry')
                    ->icon('heroicon-m-arrow-path')
                    ->requiresConfirmation()
                    ->visible(fn($record) => $record->status === 'failed')
                    ->action(function ($record) {
                        $record->update([
                            'status'        => 'pending',
                            'sent_at'       => null,
                            'failed_at'     => null,
                            'error_message' => null,
                        ]);

                        SendGuestLetterJob::dispatchSync($record->id);

                        Notification::make()
                            ->title('Retry queued')
                            ->success()
                            ->send();
                    }),

                Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-m-x-circle')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->visible(fn($record) => $record->status === 'pending' && ! is_null($record->scheduled_for))
                    ->action(function ($record) {
                        $record->update(['status' => 'cancelled']);

                        Notification::make()
                            ->title('Cancelled')
                            ->success()
                            ->send();
                    }),
            ])
            ->paginated(false);
    }
}
