# Quick Reference: Module Structure & Imports

## Module Locations

| Module      | Path                       | Purpose                                 |
| ----------- | -------------------------- | --------------------------------------- |
| Newsletter  | `app/Modules/Newsletter/`  | Newsletter campaigns, subscribers, tags |
| GuestLetter | `app/Modules/GuestLetter/` | Booking-triggered guest emails          |
| Birthday    | `app/Modules/Birthday/`    | Birthday email automation               |

## Import Cheat Sheet

### Newsletter Module

```php
// Models
use App\Modules\Newsletter\Models\Newsletter;
use App\Modules\Newsletter\Models\Subscriber;
use App\Modules\Newsletter\Models\NewsletterSend;
use App\Modules\Newsletter\Models\Tag;

// Jobs & Mail
use App\Modules\Newsletter\Jobs\SendNewsletterJob;
use App\Modules\Newsletter\Mail\NewsletterMail;
```

### GuestLetter Module

```php
// Models
use App\Modules\GuestLetter\Models\Booking;
use App\Modules\GuestLetter\Models\GuestLetterSend;
use App\Modules\GuestLetter\Models\LetterSchedule;
use App\Modules\GuestLetter\Models\LetterTemplate;

// Jobs & Mail
use App\Modules\GuestLetter\Jobs\SendGuestLetterJob;
use App\Modules\GuestLetter\Mail\ConfirmationLetterMail;
use App\Modules\GuestLetter\Mail\PreArrivalLetterMail;
use App\Modules\GuestLetter\Mail\PostStayLetterMail;

// Observers & Commands
use App\Modules\GuestLetter\Observers\BookingObserver;
use App\Modules\GuestLetter\Console\Commands\DispatchDueGuestLetters;
```

### Birthday Module

```php
// Models
use App\Modules\Birthday\Models\BirthdaySend;

// Jobs & Mail
use App\Modules\Birthday\Jobs\SendBirthdayEmailJob;
use App\Modules\Birthday\Mail\BirthdayMail;
```

### Shared Models (Still in app/Models/)

```php
use App\Models\Guest;
use App\Models\Room;
use App\Models\User;
use App\Models\Reservation;
```

## File Structure

```
app/Modules/Newsletter/
├── Models/
│   ├── Newsletter.php
│   ├── NewsletterSend.php
│   ├── Subscriber.php
│   └── Tag.php
├── Jobs/
│   └── SendNewsletterJob.php
└── Mail/
    └── NewsletterMail.php

app/Modules/GuestLetter/
├── Models/
│   ├── Booking.php
│   ├── GuestLetterSend.php
│   ├── LetterSchedule.php
│   └── LetterTemplate.php
├── Jobs/
│   └── SendGuestLetterJob.php
├── Mail/
│   ├── ConfirmationLetterMail.php
│   ├── PreArrivalLetterMail.php
│   └── PostStayLetterMail.php
├── Observers/
│   └── BookingObserver.php
└── Console/Commands/
    └── DispatchDueGuestLetters.php

app/Modules/Birthday/
├── Models/
│   └── BirthdaySend.php
├── Jobs/
│   └── SendBirthdayEmailJob.php
└── Mail/
    └── BirthdayMail.php
```

## Common Tasks

### Adding a new Newsletter model

1. Create file in `app/Modules/Newsletter/Models/YourModel.php`
2. Use namespace: `namespace App\Modules\Newsletter\Models;`
3. Import in other files: `use App\Modules\Newsletter\Models\YourModel;`

### Creating a new GuestLetter job

1. Create file in `app/Modules/GuestLetter/Jobs/YourJob.php`
2. Use namespace: `namespace App\Modules\GuestLetter\Jobs;`
3. Import models: `use App\Modules\GuestLetter\Models\*;`

### Adding Birthday functionality

1. Create file in `app/Modules/Birthday/`
2. Use namespace: `namespace App\Modules\Birthday\*;`
3. Reference Subscriber: `use App\Modules\Newsletter\Models\Subscriber;`

## Database Queries

Models work the same way, just use new namespace:

```php
// Old way
$booking = \App\Models\Booking::find(1);

// New way
$booking = \App\Modules\GuestLetter\Models\Booking::find(1);

// Or with use statement
use App\Modules\GuestLetter\Models\Booking;
$booking = Booking::find(1);
```

## Configuration References

```php
config('guestletter.pre_arrival_days', 3);  // Pre-arrival letter days
config('guestletter.post_stay_days', 1);    // Post-stay letter days
config('app.cron_token');                    // Cron authentication token
```

## Key Artisan Commands

```bash
# Dispatch due guest letters
php artisan guestletter:dispatch-due

# Send scheduled newsletters
php artisan newsletter:send-scheduled

# Listen to queue
php artisan queue:listen

# Full development environment
composer run dev
```

## Testing Imports in New Files

When creating a new file, verify imports work:

```bash
# Clear cache (sometimes needed for new imports)
php artisan cache:clear

# Run simple test
php artisan tinker
> use App\Modules\GuestLetter\Models\Booking;
> Booking::count()
```

## Filament Resource Pattern

```php
namespace App\Filament\GuestLetter\Resources\Bookings;

use App\Modules\GuestLetter\Models\Booking;  // ← Module namespace
use Filament\Resources\Resource;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;
    // ...
}
```

---

**Last Updated:** January 14, 2026  
**Status:** ✅ All modules active and functional
