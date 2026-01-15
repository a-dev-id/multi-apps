<?php

namespace App\Livewire;

use App\Modules\GuestLetter\Models\GuestLetterSend;
use Livewire\Component;

class GuestLetterSendsTable extends Component
{
    public int $bookingId;

    public function render()
    {
        $sends = GuestLetterSend::query()
            ->where('booking_id', $this->bookingId)
            ->orderByRaw('scheduled_for IS NULL, scheduled_for ASC')
            ->get();

        return view('livewire.guest-letter-sends-table', [
            'sends' => $sends,
        ]);
    }
}
