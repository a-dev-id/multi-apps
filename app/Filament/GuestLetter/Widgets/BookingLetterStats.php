<?php

namespace App\Filament\Widgets;

use App\Modules\GuestLetter\Models\GuestLetterSend;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BookingLetterStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(
                'Confirmation Sent',
                GuestLetterSend::where('letter_type', 'confirmation')
                    ->where('status', 'sent')
                    ->count()
            ),

            Stat::make(
                'Pre-Arrival Sent',
                GuestLetterSend::where('letter_type', 'pre_arrival')
                    ->where('status', 'sent')
                    ->count()
            ),

            Stat::make(
                'Post-Stay Sent',
                GuestLetterSend::where('letter_type', 'post_stay')
                    ->where('status', 'sent')
                    ->count()
            ),
        ];
    }
}
