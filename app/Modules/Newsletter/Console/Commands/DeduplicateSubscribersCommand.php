<?php

namespace App\Modules\Newsletter\Console\Commands;

use App\Modules\Newsletter\Services\SubscriberDeduplicationService;
use Illuminate\Console\Command;

class DeduplicateSubscribersCommand extends Command
{
    protected $signature = 'newsletter:deduplicate-subscribers';

    protected $description = 'Find and merge duplicate subscriber emails, keeping the oldest record and merging tags';

    public function handle()
    {
        $this->info('🔍 Scanning for duplicate emails...');

        $duplicates = SubscriberDeduplicationService::findDuplicates();

        if (empty($duplicates)) {
            $this->info('✅ No duplicate emails found!');
            return 0;
        }

        $this->error(count($duplicates) . ' duplicate email(s) found:');
        foreach ($duplicates as $email) {
            $this->line('  - ' . $email);
        }

        if (! $this->confirm('Do you want to merge these duplicates?', false)) {
            $this->info('Cancelled.');
            return 1;
        }

        $stats = SubscriberDeduplicationService::mergeAllDuplicates();

        $this->info("\n✅ Deduplication complete!");
        $this->line('Total duplicates processed: ' . $stats['total_duplicates_found']);
        $this->line('Successfully merged: ' . (count($stats['merged_emails']) - $stats['error_count']));
        $this->line('Errors: ' . $stats['error_count']);

        foreach ($stats['merged_emails'] as $item) {
            if (isset($item['error'])) {
                $this->error('  ❌ ' . $item['email'] . ': ' . $item['error']);
            } else {
                $this->info('  ✓ ' . $item['email'] . ' (Primary ID: ' . $item['primary_id'] . ')');
            }
        }

        return 0;
    }
}
