<?php

namespace App\Modules\Newsletter\Services;

use App\Modules\Newsletter\Models\Subscriber;
use Illuminate\Support\Facades\DB;

class SubscriberDeduplicationService
{
    /**
     * Find all duplicate emails in the subscribers table
     *
     * @return array Array of emails that appear more than once
     */
    public static function findDuplicates(): array
    {
        return Subscriber::query()
            ->select('email', DB::raw('COUNT(*) as count'))
            ->groupBy('email')
            ->having('count', '>', 1)
            ->pluck('email')
            ->toArray();
    }

    /**
     * Merge duplicate subscribers with the same email
     * Keeps the oldest record and transfers tags from duplicates to the primary record
     *
     * @param string $email The email address to merge
     * @return int The primary subscriber ID
     */
    public static function mergeDuplicatesByEmail(string $email): int
    {
        $email = strtolower(trim($email));

        $subscribers = Subscriber::query()
            ->where('email', $email)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($subscribers->count() <= 1) {
            return $subscribers->first()?->id ?? 0;
        }

        // Keep the oldest, delete the rest
        $primary = $subscribers->first();
        $duplicates = $subscribers->skip(1);

        // Collect all tags from duplicates
        $allTagIds = [];
        foreach ($duplicates as $duplicate) {
            $tagIds = $duplicate->tags()->pluck('id')->toArray();
            $allTagIds = array_merge($allTagIds, $tagIds);
        }

        // Attach all tags to the primary subscriber (without detaching existing)
        if (! empty($allTagIds)) {
            $primary->tags()->syncWithoutDetaching(array_unique($allTagIds));
        }

        // Update primary with best non-null values from duplicates
        $updateData = [];

        foreach ($duplicates as $duplicate) {
            // Use non-empty values from duplicates if primary doesn't have them
            if (empty($primary->name) && ! empty($duplicate->name)) {
                $updateData['name'] = $duplicate->name;
            }
            if (empty($primary->birth_date) && ! empty($duplicate->birth_date)) {
                $updateData['birth_date'] = $duplicate->birth_date;
            }
            if (empty($primary->country_code) && ! empty($duplicate->country_code)) {
                $updateData['country_code'] = $duplicate->country_code;
            }

            // Prefer is_active=true
            if (! $primary->is_active && $duplicate->is_active) {
                $updateData['is_active'] = true;
            }
        }

        if (! empty($updateData)) {
            $primary->update($updateData);
        }

        // Delete duplicate subscribers
        $deleteIds = $duplicates->pluck('id')->toArray();
        Subscriber::whereIn('id', $deleteIds)->delete();

        return $primary->id;
    }

    /**
     * Merge all duplicate emails in the subscribers table
     *
     * @return array Statistics about merged subscribers
     */
    public static function mergeAllDuplicates(): array
    {
        $duplicates = self::findDuplicates();

        $stats = [
            'total_duplicates_found' => count($duplicates),
            'merged_emails' => [],
            'error_count' => 0,
        ];

        foreach ($duplicates as $email) {
            try {
                $primaryId = self::mergeDuplicatesByEmail($email);
                $stats['merged_emails'][] = [
                    'email' => $email,
                    'primary_id' => $primaryId,
                ];
            } catch (\Exception $e) {
                $stats['error_count']++;
                $stats['merged_emails'][] = [
                    'email' => $email,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $stats;
    }

    /**
     * Ensure email uniqueness - called during import to prevent duplicates within the same batch
     * Returns normalized email to use
     *
     * @param string $email
     * @return string Normalized email
     */
    public static function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }
}
