<?php

namespace App\Filament\Newsletter\Pages;

use App\Filament\Newsletter\Widgets\NewsletterStats;
use App\Filament\Newsletter\Widgets\NewsletterOpenStats;
use App\Filament\Newsletter\Widgets\NewsletterQueueStats;
use App\Filament\Newsletter\Widgets\TodayBirthdaysWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    // ✅ removes “Welcome” and Filament cards
    protected function getHeaderWidgets(): array
    {
        return [];
    }

    // ✅ controls what widgets show on the page + order
    public function getWidgets(): array
    {
        return [
            NewsletterStats::class,      // the 3 small stats
            NewsletterOpenStats::class,  // the performance table
            NewsletterQueueStats::class,
            TodayBirthdaysWidget::class,
        ];
    }
}
