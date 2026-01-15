<?php

/**
 * Module Migration Update Script
 * 
 * This script updates all imports from old namespaces to the new module namespaces.
 * Run from project root: php update_module_imports.php
 */

$replacements = [
    // Newsletter Models
    'use App\Models\Newsletter;' => 'use App\Modules\Newsletter\Models\Newsletter;',
    'use App\Models\Subscriber;' => 'use App\Modules\Newsletter\Models\Subscriber;',
    'use App\Models\NewsletterSend;' => 'use App\Modules\Newsletter\Models\NewsletterSend;',
    'use App\Models\Tag;' => 'use App\Modules\Newsletter\Models\Tag;',

    // GuestLetter Models
    'use App\Models\Booking;' => 'use App\Modules\GuestLetter\Models\Booking;',
    'use App\Models\GuestLetterSend;' => 'use App\Modules\GuestLetter\Models\GuestLetterSend;',
    'use App\Models\LetterSchedule;' => 'use App\Modules\GuestLetter\Models\LetterSchedule;',
    'use App\Models\LetterTemplate;' => 'use App\Modules\GuestLetter\Models\LetterTemplate;',

    // Birthday Models
    'use App\Models\BirthdaySend;' => 'use App\Modules\Birthday\Models\BirthdaySend;',

    // Newsletter Jobs
    'use App\Jobs\SendNewsletterJob;' => 'use App\Modules\Newsletter\Jobs\SendNewsletterJob;',

    // GuestLetter Jobs
    'use App\Jobs\SendGuestLetterJob;' => 'use App\Modules\GuestLetter\Jobs\SendGuestLetterJob;',

    // Birthday Jobs
    'use App\Jobs\SendBirthdayEmailJob;' => 'use App\Modules\Birthday\Jobs\SendBirthdayEmailJob;',

    // Newsletter Mail
    'use App\Mail\NewsletterMail;' => 'use App\Modules\Newsletter\Mail\NewsletterMail;',

    // GuestLetter Mail
    'use App\Mail\GuestLetter\ConfirmationLetterMail;' => 'use App\Modules\GuestLetter\Mail\ConfirmationLetterMail;',
    'use App\Mail\GuestLetter\PreArrivalLetterMail;' => 'use App\Modules\GuestLetter\Mail\PreArrivalLetterMail;',
    'use App\Mail\GuestLetter\PostStayLetterMail;' => 'use App\Modules\GuestLetter\Mail\PostStayLetterMail;',

    // Birthday Mail
    'use App\Mail\BirthdayMail;' => 'use App\Modules\Birthday\Mail\BirthdayMail;',

    // Observers
    'use App\Observers\BookingObserver;' => 'use App\Modules\GuestLetter\Observers\BookingObserver;',
];

// Directories to scan
$directories = [
    'app/Filament',
    'app/Http',
    'app/Console',
    'database',
];

$files_updated = 0;
$files_skipped = 0;

foreach ($directories as $dir) {
    if (!is_dir($dir)) continue;

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $file) {
        if ($file->isDir()) continue;
        if ($file->getExtension() !== 'php') continue;

        $path = $file->getPathname();
        $content = file_get_contents($path);
        $original_content = $content;

        // Apply all replacements
        foreach ($replacements as $old => $new) {
            if (strpos($content, $old) !== false) {
                $content = str_replace($old, $new, $content);
            }
        }

        // Write back if changed
        if ($content !== $original_content) {
            file_put_contents($path, $content);
            $files_updated++;
            echo "✓ Updated: $path\n";
        } else {
            $files_skipped++;
        }
    }
}

echo "\n";
echo "=== Migration Complete ===\n";
echo "Files updated: $files_updated\n";
echo "Files checked (no changes needed): $files_skipped\n";
echo "\nNext steps:\n";
echo "1. Run: composer dump-autoload\n";
echo "2. Run: php artisan config:clear\n";
echo "3. Run: composer run test\n";
