<?php

namespace App\Filament\Newsletter\Widgets;

use App\Modules\Newsletter\Models\Newsletter;
use App\Modules\Newsletter\Models\NewsletterSend;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class OpenCountryPie extends ChartWidget
{
    protected ?string $heading = 'Opens by Country';
    protected static ?int $sort = 3;

    public ?string $filter = null;

    public function mount(): void
    {
        parent::mount();
        $this->filter ??= (string) (Newsletter::query()->orderByDesc('id')->value('id') ?? '');
    }

    protected function getFilters(): ?array
    {
        return Newsletter::query()
            ->orderByDesc('id')
            ->get(['id', 'subject'])
            ->mapWithKeys(fn($n) => [
                (string) $n->id => Str::limit((string) $n->subject, 45),
            ])
            ->toArray();
    }

    protected function getData(): array
    {
        $newsletterId = (int) ($this->filter ?? 0);

        $q = NewsletterSend::query()
            ->when($newsletterId > 0, fn($x) => $x->where('newsletter_id', $newsletterId))
            ->join('nl_subscribers', 'nl_subscribers.id', '=', 'nl_newsletter_sends.subscriber_id')
            ->whereNotNull('nl_subscribers.country_code')
            ->where('nl_subscribers.country_code', '!=', '');

        // Only count opens
        if (Schema::hasColumn('nl_newsletter_sends', 'opened_at')) {
            $q->whereNotNull('nl_newsletter_sends.opened_at');
        } elseif (Schema::hasColumn('nl_newsletter_sends', 'open_count')) {
            $q->where('nl_newsletter_sends.open_count', '>', 0);
        }

        $rows = $q->selectRaw('nl_subscribers.country_code as country, COUNT(*) as total')
            ->groupBy('country')
            ->orderByDesc('total')
            ->get();

        if ($rows->isEmpty()) {
            return [
                'labels' => ['No opens yet'],
                'datasets' => [
                    [
                        'label' => 'Opens',
                        'data' => [1],
                        'backgroundColor' => ['#4b5563'],
                        'borderWidth' => 1,
                    ],
                ],
            ];
        }

        // Top 8 + group the rest as OTHER (prevents messy pie)
        $top = $rows->take(8);
        $otherTotal = $rows->slice(8)->sum('total');

        $labels = $top->pluck('country')->toArray();
        $values = $top->pluck('total')->toArray();

        if ($otherTotal > 0) {
            $labels[] = 'OTHER';
            $values[] = $otherTotal;
        }

        $colors = array_map(fn($c) => $this->countryColor((string) $c), $labels);

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Opens',
                    'data' => $values,
                    'backgroundColor' => $colors,
                    'borderWidth' => 1,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function countryColor(string $code): string
    {
        return match (strtoupper($code)) {
            'AUS' => '#1f77b4',
            'IND' => '#ff7f0e',
            'USA' => '#2ca02c',
            'GBR' => '#d62728',
            'DEU' => '#9467bd',
            'IDN' => '#8c564b',
            'ITA' => '#e377c2',
            'KOR' => '#7f7f7f',
            'NZL' => '#bcbd22',
            'CHN' => '#17becf',
            'CAN' => '#aec7e8',
            'FRA' => '#ffbb78',
            'IRL' => '#98df8a',
            'VNM' => '#ff9896',
            'DNK' => '#c5b0d5',
            'OTHER' => '#111827',
            default => '#4b5563',
        };
    }
}
