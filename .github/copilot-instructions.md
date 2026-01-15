# Copilot Instructions for Nandini Apps

## Project Overview

Nandini Apps is a **Laravel 12 + Filament 4 admin panel** that automates guest communication for hospitality. The system sends **transactional emails** (guest letters, newsletters, birthday wishes) through a **queue-based architecture** with scheduled tasks.

**Key domains:** Guest letter automation, newsletter management, birthday notifications.

**Architecture:** Modular structure with separate modules for each domain under `app/Modules/`

## Module Structure (Post-Refactoring)

The codebase is organized into **three domain modules**:

```
app/Modules/
├── Newsletter/          # Newsletter campaigns and subscriber management
│   ├── Models/         # Newsletter, NewsletterSend, Subscriber, Tag
│   ├── Jobs/           # SendNewsletterJob
│   └── Mail/           # NewsletterMail
│
├── GuestLetter/        # Booking-triggered guest communication
│   ├── Models/         # Booking, GuestLetterSend, LetterSchedule, LetterTemplate
│   ├── Jobs/           # SendGuestLetterJob
│   ├── Mail/           # ConfirmationLetterMail, PreArrivalLetterMail, PostStayLetterMail
│   ├── Observers/      # BookingObserver
│   └── Console/Commands/ # DispatchDueGuestLetters
│
└── Birthday/           # Birthday email automation
    ├── Models/         # BirthdaySend
    ├── Jobs/           # SendBirthdayEmailJob
    └── Mail/           # BirthdayMail
```

**Shared Models** (not in modules):

-   `Guest`, `Room`, `User`, `Reservation` — used by multiple modules, remain in `app/Models/`

## Import Patterns (Updated)

When working with models, use the module namespaces:

```php
// Newsletter module
use App\Modules\Newsletter\Models\Newsletter;
use App\Modules\Newsletter\Models\Subscriber;
use App\Modules\Newsletter\Jobs\SendNewsletterJob;
use App\Modules\Newsletter\Mail\NewsletterMail;

// GuestLetter module
use App\Modules\GuestLetter\Models\Booking;
use App\Modules\GuestLetter\Models\GuestLetterSend;
use App\Modules\GuestLetter\Jobs\SendGuestLetterJob;
use App\Modules\GuestLetter\Mail\ConfirmationLetterMail;

// Birthday module
use App\Modules\Birthday\Models\BirthdaySend;
use App\Modules\Birthday\Jobs\SendBirthdayEmailJob;
use App\Modules\Birthday\Mail\BirthdayMail;

// Shared models
use App\Models\Guest;
use App\Models\Room;
```

## Architecture & Data Flow

### Three Core Email Workflows

1. **Guest Letters** (`app/Modules/GuestLetter/Jobs/SendGuestLetterJob.php`)

    - Triggers: `BookingObserver::created()` → dispatches confirmation letter immediately
    - Scheduled letters: pre-arrival (3 days before) and post-stay (after checkout)
    - Types: `confirmation`, `pre_arrival`, `post_stay` (in `GuestLetterSend` model)

2. **Newsletters** (`app/Modules/Newsletter/Jobs/SendNewsletterJob.php`)

    - Dispatched to active `Subscriber` records via `SendNewsletterJob`
    - Deduplication: `firstOrCreate` prevents duplicate sends
    - Tracking: `sent_at`, `open_count` fields on `NewsletterSend`

3. **Birthday Emails** (`app/Modules/Birthday/Jobs/SendBirthdayEmailJob.php`)
    - Dispatched daily for subscribers with matching birth dates
    - Deduplication: uses year + subscriber to prevent re-sending

### Key Models

-   `Booking` → relates to `Guest`, `Room`; observed by `BookingObserver` (GuestLetter module)
-   `GuestLetterSend` → tracks status (`pending`, `sent`, `failed`) with `scheduled_for` timestamps (GuestLetter module)
-   `NewsletterSend` → tracks `sent_at`, `open_count` for analytics (Newsletter module)
-   `Guest` → stores `birth_date` (cast as date) [shared]
-   `Newsletter`, `Subscriber` → one-to-many with `NewsletterSend` (Newsletter module)
-   `BirthdaySend` → tracks birthday email sends per year (Birthday module)

### Queue System

-   **Driver:** Database-backed queue (default, can switch to Redis)
-   **Command:** `php artisan queue:listen --tries=1`
-   **Locking:** Uses `Cache::lock()` to prevent concurrent command execution (55-second timeout)
-   **Dev mode:** `composer run dev` runs queue listener alongside Laravel server and Vite

## Filament Admin Structure

### Pattern

-   **Resources** organized by domain (e.g., `app/Filament/GuestLetter/Resources/Bookings/`)
-   **Schema separation:** Forms in `Schemas/`, Tables in `Tables/`
-   **Relation managers** for nested resources (e.g., `GuestLetterSendsRelationManager`)

Example: [BookingResource](app/Filament/GuestLetter/Resources/Bookings/BookingResource.php) extends `Resource`, delegates `form()` to `BookingForm` schema class.

### Key Entry Points

-   **Dashboard widgets:** `app/Filament/*/Widgets/`
-   **Resources:** `app/Filament/Newsletter/Resources/` and `app/Filament/GuestLetter/Resources/`

## Critical Conventions

### 1. **Job Error Handling**

-   Jobs log start, pre-send, post-send with `Log::info('JobName ...', ['id' => $id])`
-   Email validation happens before attempting send; invalid emails marked `failed` with error message
-   Always set `sent_at` **after** successful Mail::send() to prevent duplicates
-   Located in `app/Modules/{Module}/Jobs/`

### 2. **Scheduled Tasks**

-   Register in `ScheduleServiceProvider::boot()` with `$schedule->command()`
-   Example: `newsletters:send-scheduled` runs every minute
-   Cron tokens: use `config('app.cron_token')` for external HTTP triggers
-   Commands in GuestLetter module: `app/Modules/GuestLetter/Console/Commands/`

### 3. **Status Tracking**

-   `GuestLetterSend`: `pending` → `sent`/`failed`
-   `NewsletterSend`: uses `firstOrCreate` + `sent_at` check to prevent re-sends
-   Always populate `*_at` timestamps (e.g., `failed_at`, `sent_at`)

### 4. **Environment & Configuration**

-   Mail mailer defaults to `log` (see `config/mail.php`) for testing
-   Queue table: `jobs` (configurable via `DB_QUEUE_TABLE`)
-   Guest letter pre-arrival days: `config('guestletter.pre_arrival_days', 3)`

## Development Workflow

### Common Commands

```bash
# Full setup
composer run setup

# Development (server + queue + vite watcher)
composer run dev

# Run tests
composer run test

# Queue processing (for production)
php artisan queue:listen --tries=1
php artisan queue:work --stop-when-empty --max-jobs=20 --max-time=50

# Dispatch specific letter job
php artisan guestletter:dispatch-due

# Clear cache (useful after config changes)
curl http://localhost/clear-cache
```

### Key Files to Understand First

1. [app/Modules/GuestLetter/Jobs/SendGuestLetterJob.php](app/Modules/GuestLetter/Jobs/SendGuestLetterJob.php) — Core email sending logic with guest name personalization
2. [app/Modules/GuestLetter/Mail/PostStayLetterMail.php](app/Modules/GuestLetter/Mail/PostStayLetterMail.php) — Post-stay letter with guest name in subject
3. [app/Filament/GuestLetter/Resources/PostStayLetters/PostStayLetterResource.php](app/Filament/GuestLetter/Resources/PostStayLetters/PostStayLetterResource.php) — Post-stay letter admin interface
4. [app/Filament/Newsletter/Resources/Subscribers/Pages/ListSubscribers.php](app/Filament/Newsletter/Resources/Subscribers/Pages/ListSubscribers.php) — Subscriber management with guest import
5. [ScheduleServiceProvider.php](app/Providers/ScheduleServiceProvider.php) — Task scheduler
6. [routes/web.php](routes/web.php) — Cron endpoints and cache clearing

## Recent Updates (January 15, 2026)

### Post-Stay Letter Enhancements

-   **Guest Name in Emails**: Post-stay letters now include guest's full name in subject line and body
-   **Manual Letter Creation**: Can create post-stay letters without bookings by selecting guests directly
-   **Guest Display Fields**: Form shows guest details (title, email, phone, country, birth date) when guest is selected
-   **Type Filtering**: Post-stay letters list only shows letters with `type = 'post_stay'`
-   **Guest Dropdown**: Hides guests who already have pending or sent post-stay letters

### Database Schema Changes

-   **Added `guest_id` column** to `gl_guest_letter_sends` table (migration: `2026_01_15_123448_add_guest_id_to_guest_letter_sends_table.php`)
-   **New Relationship**: `GuestLetterSend::guestDirect()` for direct guest access (no booking required)
-   **Fillable Fields Updated**: `GuestLetterSend` model now includes `guest_id` in `$fillable` array

### Newsletter Subscriber Enhancements

-   **Guest Import**: New button in Subscribers page to import guests from Guest Letter system
    -   Automatically checks for duplicate emails
    -   Only imports new guests not already in subscribers
    -   Shows import statistics (imported count, skipped count)
-   **Optimized Toolbar**: All subscriber tools grouped into a single "Tools" dropdown menu
    -   Import Guests from Letter System
    -   Import Subscribers from CSV
    -   Check & Merge Duplicates
    -   Purge Bounced CSV
-   **Tooltips**: All toolbar actions have helpful tooltips explaining their functionality

### UI/UX Improvements

-   **Page Title Updated**: Post-stay letters section now shows "Post-stay Letter Sends" instead of generic "Guest Letter Sends"
-   **Dropdown Menu**: Organizes import and maintenance tools into a clean dropdown to reduce clutter
-   **Guest Name Display**: Table shows full guest name with fallback handling for both booking-based and manually-created letters

## Important Caveats

-   **Migration Required**: The `guest_id` column must be added to `gl_guest_letter_sends` table before deploying to production
-   **Filament 4 syntax:** Uses `Schemas\Schema` (not inline field arrays)
-   **Mail queue:** Jobs are database-backed; ensure workers are running in production
-   **Lock contention:** Commands like `guestletter:dispatch-due` use 55-second locks; overlapping runs are silently skipped
-   **Email Personalization**: Guest name is extracted from title + first_name + last_name fields. Ensure guest data is complete for best email formatting.
-   **Duplicate Guest Handling**: Guest import uses email as the unique identifier. Guests with duplicate emails will be skipped.
