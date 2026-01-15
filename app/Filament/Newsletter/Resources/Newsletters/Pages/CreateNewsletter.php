<?php

namespace App\Filament\Newsletter\Resources\Newsletters\Pages;

use App\Filament\Newsletter\Resources\Newsletters\NewsletterResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNewsletter extends CreateRecord
{
    protected static string $resource = NewsletterResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
