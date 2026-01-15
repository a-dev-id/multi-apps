<?php

namespace App\Filament\Newsletter\Resources\Tags\Pages;

use App\Filament\Newsletter\Resources\Tags\TagResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTag extends CreateRecord
{
    protected static string $resource = TagResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
