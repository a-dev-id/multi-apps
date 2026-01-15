<?php

namespace App\Filament\Newsletter\Resources\Newsletters\Pages;

use App\Filament\Newsletter\Resources\Newsletters\NewsletterResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;

class EditNewsletter extends EditRecord
{
    protected static string $resource = NewsletterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            Action::make('preview')
                ->label('Preview')
                ->url(fn() => route('newsletters.preview', $this->record))
                ->openUrlInNewTab(),
            ...parent::getHeaderActions(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
