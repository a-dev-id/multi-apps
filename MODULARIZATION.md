# Module Migration Guide for Nandini Apps

This document outlines the manual refactoring completed to move files into a modular architecture.

## Completed Steps

✅ **Created Module Structure**

-   `app/Modules/Newsletter/` - Newsletter models, jobs, mail
-   `app/Modules/GuestLetter/` - Guest letter models, jobs, mail, observer, commands
-   `app/Modules/Birthday/` - Birthday models and jobs

✅ **Moved Core Files**

-   Newsletter: `Models`, `Jobs`, `Mail`
-   GuestLetter: `Models`, `Jobs`, `Mail`, `Observers`, `Console/Commands`
-   Birthday: `Models`, `Jobs`, `Mail`

✅ **Updated Autoloading**

-   Modified `composer.json` to include module PSR-4 mappings
-   Run `composer dump-autoload` to regenerate autoloader

✅ **Updated Service Providers & Controllers**

-   `AppServiceProvider.php` - Booking observer reference updated
-   HTTP Controllers - All imports updated to use module namespaces:
    -   `CronGuestLetterController`
    -   `CronNewsletterController`
    -   `CronBirthdayController`
    -   `NewsletterController`
    -   `NewsletterPreviewController`
    -   `NewsletterTrackingController`

✅ **Updated Routes & Config**

-   `routes/web.php` - Newsletter send tracking route updated
-   `app/Livewire/GuestLetterSendsTable.php` - Updated imports

## Still To Do - Filament Resources

The following Filament resources/widgets still reference old namespaces and need updating:

### Newsletter Module Filament Files

Update `use App\Models\*` to `use App\Modules\Newsletter\Models\*`:

-   `app/Filament/Newsletter/Resources/Newsletters/NewsletterResource.php` - Newsletter model
-   `app/Filament/Newsletter/Resources/Newsletters/Pages/*.php` - Newsletter model
-   `app/Filament/Newsletter/Resources/Subscribers/SubscriberResource.php` - Subscriber model
-   `app/Filament/Newsletter/Resources/Subscribers/Pages/*.php` - Subscriber model
-   `app/Filament/Newsletter/Resources/Tags/TagResource.php` - Tag model
-   `app/Filament/Newsletter/Resources/Tags/Pages/*.php` - Tag model
-   `app/Filament/Newsletter/Widgets/TodayBirthdaysWidget.php` - Subscriber model
-   `app/Filament/Newsletter/Widgets/NewsletterStats.php` - Newsletter, Subscriber models
-   `app/Filament/Newsletter/Widgets/NewsletterOpenStats.php` - Newsletter, NewsletterSend models
-   `app/Filament/Newsletter/Widgets/NewsletterQueueStats.php` - Newsletter, NewsletterSend models
-   `app/Filament/Newsletter/Widgets/OpenCountryPie.php` - Newsletter, NewsletterSend models

### GuestLetter Module Filament Files

Update `use App\Models\*` to `use App\Modules\GuestLetter\Models\*`:

-   `app/Filament/GuestLetter/Resources/Bookings/BookingResource.php` - ✅ DONE
-   `app/Filament/GuestLetter/Resources/Bookings/Pages/*.php` - Booking model
-   `app/Filament/GuestLetter/Resources/Bookings/Tables/BookingsTable.php` - GuestLetterSend model
-   `app/Filament/GuestLetter/Resources/Bookings/Schemas/BookingForm.php` - Guest, Room models
-   `app/Filament/GuestLetter/Resources/Bookings/RelationManagers/GuestLetterSendsRelationManager.php` - SendGuestLetterJob job class
-   `app/Filament/GuestLetter/Resources/Guests/GuestResource.php` - Guest model
-   `app/Filament/GuestLetter/Resources/Guests/Pages/*.php` - Guest model
-   `app/Filament/GuestLetter/Resources/Rooms/RoomResource.php` - Room model
-   `app/Filament/GuestLetter/Resources/Rooms/Pages/*.php` - Room model
-   `app/Filament/GuestLetter/Widgets/BookingLetterTable.php` - Booking model
-   `app/Filament/GuestLetter/Widgets/BookingLetterStats.php` - GuestLetterSend model

## Update Pattern

For each file, replace:

```php
use App\Models\ModelName;
```

With:

```php
use App\Modules\Module\Models\ModelName;
```

For jobs:

```php
use App\Jobs\SendGuestLetterJob;
```

With:

```php
use App\Modules\GuestLetter\Jobs\SendGuestLetterJob;
```

For mail:

```php
use App\Mail\NewsletterMail;
```

With:

```php
use App\Modules\Newsletter\Mail\NewsletterMail;
```

## Notes

-   **Guest and Room Models**: These are still in `app/Models/` (not moved to modules) as they're shared across modules
-   **User Model**: Remains in `app/Models/` as it's a core system model
-   The old files in `app/Jobs/`, `app/Mail/`, `app/Models/`, `app/Observers/` can be deleted once all imports are updated
-   Run tests after updating to verify everything works: `composer run test`
-   Clear config cache: `php artisan config:clear`
-   Regenerate autoloader: `composer dump-autoload`
