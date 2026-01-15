<?php

namespace App\Filament\GuestLetter\Resources\Bookings\Tables;

use App\Modules\GuestLetter\Models\GuestLetterSend;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class BookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('booking_number')
                    ->label('Booking No.')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('guest.full_name')
                    ->label('Guest')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('room.name')
                    ->label('Room')
                    ->sortable(),

                Tables\Columns\TextColumn::make('arrival_date')
                    ->label('Arrival')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('departure_date')
                    ->label('Departure')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('confirmation_sent_at')
                    ->label('Confirmation Sent')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('send')
                    ->label('Send')
                    ->icon('heroicon-o-paper-airplane')
                    ->requiresConfirmation()

                    // IMPORTANT: disable based on "already scheduled/sent", not confirmation_sent_at
                    ->disabled(function ($record) {
                        return GuestLetterSend::query()
                            ->where('booking_id', $record->id)
                            ->where('type', 'confirmation')
                            ->whereIn('status', ['pending', 'sent'])
                            ->exists();
                    })

                    ->action(function ($record) {
                        $toEmail = $record->guest?->email;

                        if (! $toEmail) {
                            Notification::make()
                                ->title('Guest email not found')
                                ->danger()
                                ->send();

                            return;
                        }

                        $arrival = Carbon::parse($record->arrival_date);
                        $departure = Carbon::parse($record->departure_date);

                        // Confirmation: immediate
                        GuestLetterSend::updateOrCreate(
                            ['booking_id' => $record->id, 'type' => 'confirmation'],
                            [
                                'to_email'      => $toEmail,
                                'scheduled_for' => now(),
                                'status'        => 'pending',
                                'sent_at'       => null,
                                'failed_at'     => null,
                                'error_message' => null,
                            ]
                        );

                        // Pre-arrival: 14 days before check-in,
                        // if that date is already past, send 3 days before check-in
                        $preArrivalAt = $arrival->copy()->subDays(14)->startOfDay();

                        if ($preArrivalAt->isPast()) {
                            $preArrivalAt = $arrival->copy()->subDays(3)->startOfDay();
                        }

                        GuestLetterSend::updateOrCreate(
                            ['booking_id' => $record->id, 'type' => 'pre_arrival'],
                            [
                                'to_email'      => $toEmail,
                                'scheduled_for' => $preArrivalAt,
                                'status'        => 'pending',
                                'sent_at'       => null,
                                'failed_at'     => null,
                                'error_message' => null,
                            ]
                        );


                        // Post-stay: 1 day after departure
                        $postStayAt = $departure->copy()->addDay()->startOfDay();

                        GuestLetterSend::updateOrCreate(
                            ['booking_id' => $record->id, 'type' => 'post_stay'],
                            [
                                'to_email'      => $toEmail,
                                'scheduled_for' => $postStayAt,
                                'status'        => 'pending',
                                'sent_at'       => null,
                                'failed_at'     => null,
                                'error_message' => null,
                            ]
                        );

                        Notification::make()
                            ->title('Scheduled: confirmation + pre-arrival + post-stay')
                            ->success()
                            ->send();
                    }),

                ViewAction::make()
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('warning'),

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
