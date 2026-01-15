<?php

namespace App\Filament\GuestLetter\Resources\PostStayLetters\Pages;

use App\Filament\GuestLetter\Resources\PostStayLetters\PostStayLetterResource;
use Filament\Resources\Pages\CreateRecord;

class SendPostStayLetter extends CreateRecord
{
    protected static string $resource = PostStayLetterResource::class;
}
