<?php

namespace App\Filament\Newsletter\Widgets;

use App\Modules\Newsletter\Models\Newsletter;
use App\Modules\Newsletter\Models\NewsletterSend;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Widgets\Widget;
use Illuminate\Support\Str;

class NewsletterQueueStats extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static ?int $sort = 1;

    protected string $view = 'filament.newsletter.widgets.newsletter-queue-stats';

    protected int|string|array $columnSpan = 'full';

    public ?string $newsletterId = null;

    public function mount(): void
    {
        $this->newsletterId ??= (string) (Newsletter::query()->latest('id')->value('id') ?? '');
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('newsletterId')
                ->label('Newsletter')
                ->options(
                    Newsletter::query()
                        ->orderByDesc('id')
                        ->get(['id', 'subject'])
                        ->mapWithKeys(fn($n) => [
                            (string) $n->id => Str::limit((string) $n->subject, 45),
                        ])
                        ->toArray()
                )
                ->searchable()
                ->preload()
                ->native(false)
                ->live(),
        ];
    }

    public function getSentCountProperty(): int
    {
        $id = (int) ($this->newsletterId ?? 0);

        return NewsletterSend::query()
            ->where('newsletter_id', $id)
            ->whereNotNull('sent_at')
            ->where('failed', 0)
            ->count();
    }

    public function getPendingCountProperty(): int
    {
        $id = (int) ($this->newsletterId ?? 0);

        return NewsletterSend::query()
            ->where('newsletter_id', $id)
            ->whereNull('sent_at')
            ->where('failed', 0)
            ->count();
    }

    public function getFailedCountProperty(): int
    {
        $id = (int) ($this->newsletterId ?? 0);

        return NewsletterSend::query()
            ->where('newsletter_id', $id)
            ->where('failed', 1)
            ->count();
    }
}
