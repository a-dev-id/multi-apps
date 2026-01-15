<?php

namespace App\Filament\GuestLetter\Resources\Guests\Pages;

use App\Filament\GuestLetter\Resources\Guests\GuestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGuest extends CreateRecord
{
    protected static string $resource = GuestResource::class;
}
