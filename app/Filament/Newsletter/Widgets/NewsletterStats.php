<?php

namespace App\Filament\Newsletter\Widgets;

use App\Modules\Newsletter\Models\Newsletter;
use App\Modules\Newsletter\Models\Subscriber;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class NewsletterStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Subscribers', Subscriber::count())
                ->icon('heroicon-o-users'),

            Stat::make('Active Subscribers', Subscriber::where('is_active', true)->count())
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            Stat::make('Newsletters Sent', Newsletter::count())
                ->icon('heroicon-o-paper-airplane'),
        ];
    }
}
