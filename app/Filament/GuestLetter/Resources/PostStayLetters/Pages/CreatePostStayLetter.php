<?php

namespace App\Filament\GuestLetter\Resources\PostStayLetters\Pages;

use App\Filament\GuestLetter\Resources\PostStayLetters\PostStayLetterResource;
use App\Models\Guest;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;

class CreatePostStayLetter extends CreateRecord
{
    protected static string $resource = PostStayLetterResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = 'post_stay';
        $data['status'] = 'pending';
        $data['booking_id'] = null;

        // Get email from the selected guest
        if (!empty($data['guest_id'])) {
            $guest = Guest::find($data['guest_id']);
            if ($guest) {
                $data['to_email'] = $guest->email;
            }
        }

        // Set scheduled_for to +1 day from departure if not already set
        if (empty($data['scheduled_for']) && !empty($data['departure_date'])) {
            $data['scheduled_for'] = Carbon::parse($data['departure_date'])->addDay();
        }

        // Keep guest_id for email personalization, remove other UI fields
        unset($data['booking_number']);
        unset($data['arrival_date']);
        unset($data['departure_date']);
        unset($data['guest_title']);
        unset($data['guest_first_name']);
        unset($data['guest_last_name']);
        unset($data['guest_email']);
        unset($data['guest_phone']);
        unset($data['guest_country']);
        unset($data['guest_birth_date']);

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Post-stay letter created';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
