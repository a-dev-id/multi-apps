<?php

namespace App\Filament\Newsletter\Widgets;

use App\Modules\Newsletter\Models\Subscriber;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class TodayBirthdaysWidget extends TableWidget
{
    protected static ?string $heading = 'Today’s Birthdays';
    protected static ?int $sort = 20;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $tz = 'Asia/Singapore'; // GMT+8
        $now = Carbon::now($tz);
        $month = (int) $now->format('m');
        $day   = (int) $now->format('d');
        $year  = (int) $now->format('Y');

        return $table
            ->query($this->getQuery($month, $day, $year))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->default('-')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('birth_date')
                    ->label('Birth Date')
                    ->date('d M Y'),

                Tables\Columns\TextColumn::make('birthday_status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn(?string $state) => match ($state) {
                        'sent' => 'Sent',
                        'failed' => 'Failed',
                        default => 'Pending',
                    })
                    ->color(fn(?string $state) => match ($state) {
                        'sent' => 'success',
                        'failed' => 'danger',
                        default => 'warning',
                    }),

                Tables\Columns\TextColumn::make('sent_at')
                    ->label('Sent At')
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('-'),
            ])
            ->defaultSort('name', 'asc')
            ->paginated(false);
    }

    private function getQuery(int $month, int $day, int $year): Builder
    {
        return Subscriber::query()
            ->where('is_active', true)
            ->whereNotNull('birth_date')
            ->whereMonth('birth_date', $month)
            ->whereDay('birth_date', $day)
            ->leftJoin('bd_birthday_sends as bs', function ($join) use ($year) {
                $join->on('bs.subscriber_id', '=', 'nl_subscribers.id')
                    ->where('bs.year', '=', $year);
            })
            ->select([
                'nl_subscribers.*',
                'bs.status as birthday_status',
                'bs.sent_at as sent_at',
            ]);
    }
}
