<?php

namespace App\Filament\Newsletter\Widgets;

use App\Modules\Newsletter\Models\Newsletter;
use App\Modules\Newsletter\Models\NewsletterSend;
use Filament\Tables;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class NewsletterOpenStats extends TableWidget
{
    protected static ?string $heading = 'Newsletter Performance';

    // protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    protected function getTableQuery(): Builder
    {
        return Newsletter::query()
            ->withSum('sends as total_opens', 'open_count')
            ->addSelect([
                // Use sent_at if exists, fallback to created_at if sent_at is NULL
                'sent_at' => NewsletterSend::query()
                    ->selectRaw('MIN(COALESCE(sent_at, created_at))')
                    ->whereColumn('newsletter_id', 'nl_newsletters.id'),
            ]);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('subject')
                ->label('Newsletter')
                ->limit(30) // adjust
                ->tooltip(fn($state) => $state)
                ->searchable(),

            Tables\Columns\TextColumn::make('sent_at')
                ->label('Sent Date')
                ->dateTime('d M Y h:i A')
                ->sortable()
                ->placeholder('-'),

            Tables\Columns\TextColumn::make('total_opens')
                ->label('Total Opens'),
        ];
    }
}
