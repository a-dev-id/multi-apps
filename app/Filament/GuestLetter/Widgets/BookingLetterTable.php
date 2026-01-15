<?php

namespace App\Filament\GuestLetter\Widgets;

use App\Modules\GuestLetter\Models\Booking;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class BookingLetterTable extends TableWidget
{
    protected static ?string $heading = 'Booking Letter Table';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->baseQuery())
            ->columns([
                TextColumn::make('booking_number')
                    ->label('Booking')
                    ->searchable()
                    ->extraAttributes(['class' => 'text-sm']),

                TextColumn::make('guest_name')
                    ->label('Guest')
                    ->searchable()
                    ->extraAttributes(['class' => 'text-sm']),

                TextColumn::make('guest_email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->extraAttributes(['class' => 'text-sm']),

                TextColumn::make('letters_summary')
                    ->label('Letters')
                    ->html()
                    ->getStateUsing(fn($record) => new HtmlString(
                        $this->lettersSummaryHtml(
                            $record->post_stay_status ?? null,
                            $record->pre_arrival_status ?? null,
                            $record->confirmation_status ?? null,
                        )
                    ))
                    ->extraAttributes(['class' => 'text-sm whitespace-nowrap']),
            ])
            ->defaultSort('gl_bookings.created_at', 'desc')
            ->paginated([5, 10, 25]);
    }

    private function baseQuery()
    {
        return Booking::query()
            ->select([
                'gl_bookings.id',
                'gl_bookings.booking_number',
                DB::raw("TRIM(CONCAT_WS(' ', guests.title, guests.first_name, guests.last_name)) as guest_name"),
                DB::raw("guests.email as guest_email"),

                DB::raw("MAX(CASE WHEN gls.type = 'confirmation' THEN gls.status END) as confirmation_status"),
                DB::raw("MAX(CASE WHEN gls.type = 'pre_arrival'  THEN gls.status END) as pre_arrival_status"),
                DB::raw("MAX(CASE WHEN gls.type = 'post_stay'    THEN gls.status END) as post_stay_status"),
            ])
            ->leftJoin('guests', 'guests.id', '=', 'gl_bookings.guest_id')
            ->leftJoin('gl_guest_letter_sends as gls', 'gls.booking_id', '=', 'gl_bookings.id')
            ->groupBy(
                'gl_bookings.id',
                'gl_bookings.booking_number',
                'guests.title',
                'guests.first_name',
                'guests.last_name',
                'guests.email'
            );
    }

    private function lettersSummaryHtml(?string $postStay, ?string $preArrival, ?string $confirmation): string
    {
        return implode(
            '<span style="display:inline-block;width:8px;"></span>',
            [
                $this->pill('Post Stay', $postStay),
                $this->pill('Pre Arrival', $preArrival),
                $this->pill('Confirmation', $confirmation),
            ]
        );
    }


    private function pill(string $label, ?string $status): string
    {
        [$statusText, $bg, $ring, $fg] = match ($status) {
            'sent' => [
                'Sent',
                'rgba(16, 185, 129, 0.18)',   // emerald
                'rgba(16, 185, 129, 0.45)',
                'rgb(209, 250, 229)',
            ],
            'pending' => [
                'Pending',
                'rgba(245, 158, 11, 0.18)',   // amber
                'rgba(245, 158, 11, 0.45)',
                'rgb(254, 243, 199)',
            ],
            'failed' => [
                'Failed',
                'rgba(239, 68, 68, 0.18)',    // red
                'rgba(239, 68, 68, 0.45)',
                'rgb(254, 226, 226)',
            ],
            default => [
                'Not Set',
                'rgba(148, 163, 184, 0.14)',  // gray
                'rgba(148, 163, 184, 0.30)',
                'rgb(226, 232, 240)',
            ],
        };

        $label = e($label);

        // Fully rounded pill + consistent spacing
        return <<<HTML
<span
  class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-[11px] font-medium tracking-wide"
  style="
    background: {$bg};
    color: {$fg};
    box-shadow:
      inset 0 0 0 1px {$ring},
      0 0 0 1px rgba(0,0,0,0.05);
  "
>
  <span class="opacity-85">{$label}</span>
  <span class="font-semibold">{$statusText}</span>
</span>
HTML;
    }
}
