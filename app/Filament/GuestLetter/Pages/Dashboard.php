<?php

namespace App\Filament\GuestLetter\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\GuestLetter\Widgets\BookingLetterStats;
use App\Filament\GuestLetter\Widgets\BookingLetterTable;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            // BookingLetterStats::class,
            BookingLetterTable::class,
        ];
    }
}
