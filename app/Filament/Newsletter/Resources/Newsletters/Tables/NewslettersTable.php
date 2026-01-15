<?php

namespace App\Filament\Newsletter\Resources\Newsletters\Tables;

use App\Modules\Newsletter\Jobs\SendNewsletterJob;
use App\Modules\Newsletter\Models\NewsletterSend;
use App\Modules\Newsletter\Models\Subscriber;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NewslettersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subject')->searchable(),

                TextColumn::make('scheduled_at')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('sent_at')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),

                Action::make('send')
                    ->label('Send')
                    ->icon('heroicon-o-paper-airplane')
                    ->requiresConfirmation()
                    ->visible(fn($record) => $record->sent_at === null)
                    ->action(function ($record): void {

                        $query = Subscriber::query()
                            ->where('is_active', true);

                        // Audience: tags
                        if ($record->audience_type === 'tags') {
                            $tagIds = (array) ($record->tag_ids ?? []);

                            $query->when(! empty($tagIds), function ($q) use ($tagIds) {
                                $q->whereHas('tags', function ($t) use ($tagIds) {
                                    $t->whereIn('tags.id', $tagIds);
                                });
                            });
                        }

                        // Audience: country
                        if ($record->audience_type === 'country') {
                            $codes = (array) ($record->country_codes ?? []);

                            $query->when(! empty($codes), function ($q) use ($codes) {
                                $q->whereIn('country_code', $codes);
                            });
                        }

                        // Audience: year (only if your subscribers table has guest_year)
                        if ($record->audience_type === 'year') {
                            $year = $record->guest_year;

                            $query->when(! empty($year), function ($q) use ($year) {
                                $q->where('guest_year', $year);
                            });
                        }

                        $count = 0;

                        $query->select(['id', 'email', 'name'])
                            ->orderBy('id')
                            ->chunkById(200, function ($subs) use ($record, &$count) {
                                foreach ($subs as $subscriber) {

                                    // ✅ Pre-create row so Pending/Sent/Failed widget is accurate
                                    NewsletterSend::firstOrCreate(
                                        [
                                            'newsletter_id' => $record->id,
                                            'subscriber_id' => $subscriber->id,
                                        ],
                                        [
                                            'sent_at' => null,
                                            'open_count' => 0,
                                            'failed' => 0,
                                            'error_message' => null,
                                        ]
                                    );

                                    // ✅ Dispatch job (job will set sent_at after success)
                                    dispatch(new SendNewsletterJob($record, $subscriber));

                                    $count++;
                                }
                            });

                        // ✅ Keep newsletter.sent_at as "queued at" (NOT "fully sent")
                        $record->update(['sent_at' => now()]);

                        Notification::make()
                            ->title('Queued')
                            ->body("Queued {$count} email(s) based on audience: {$record->audience_type}.")
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
