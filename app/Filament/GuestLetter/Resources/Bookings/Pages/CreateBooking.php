<?php

namespace App\Filament\GuestLetter\Resources\Bookings\Pages;

use App\Filament\GuestLetter\Resources\Bookings\BookingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;
}
