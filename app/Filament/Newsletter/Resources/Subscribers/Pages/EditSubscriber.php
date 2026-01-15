<?php

namespace App\Filament\Newsletter\Resources\Subscribers\Pages;

use App\Filament\Newsletter\Resources\Subscribers\SubscriberResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSubscriber extends EditRecord
{
    protected static string $resource = SubscriberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
