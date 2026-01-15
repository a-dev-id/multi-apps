<?php

namespace App\Filament\GuestLetter\Resources\PostStayLetters\Pages;

use App\Filament\GuestLetter\Resources\PostStayLetters\PostStayLetterResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPostStayLetters extends ListRecords
{
    protected static string $resource = PostStayLetterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
