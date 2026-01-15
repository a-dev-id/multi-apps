<?php

namespace App\Filament\Newsletter\Resources\Subscribers\Pages;

use App\Filament\Newsletter\Resources\Subscribers\SubscriberResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSubscriber extends CreateRecord
{
    protected static string $resource = SubscriberResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
