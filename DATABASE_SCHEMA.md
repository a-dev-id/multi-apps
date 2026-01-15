# Database Schema with System Prefixes

**Last Updated:** January 14, 2026  
**Status:** ✅ Schema applied and verified

## Prefix Convention

-   **`nl_`** = Newsletter system (campaigns, subscribers, tags)
-   **`gl_`** = GuestLetter system (bookings, guest letters)
-   **`bd_`** = Birthday system (birthday tracking)

---

## Newsletter System Tables

### `newsletters` (nl\_)

Newsletter campaign information

-   `id` - Primary key
-   `nl_subject` - Campaign subject line
-   `nl_body_html` - HTML content
-   `nl_scheduled_at` - When to send
-   `nl_sent_at` - When actually sent
-   `nl_tag_id` - Associated tag
-   `nl_audience_type` - all, country, year, tags
-   `nl_country_codes` - JSON array of target countries
-   `nl_send_to_all` - Boolean
-   `created_at`, `updated_at`

### `newsletter_sends` (nl\_)

Tracking individual newsletter sends to subscribers

-   `id` - Primary key
-   `nl_newsletter_id` - Foreign key to newsletters
-   `nl_subscriber_id` - Foreign key to subscribers
-   `nl_email` - Recipient email (denormalized)
-   `nl_sent_at` - When sent
-   `nl_open_count` - Times opened
-   `nl_opened_at` - First open time
-   `nl_last_open_ip` - Last opener IP
-   `nl_last_open_country` - Last opener country
-   `nl_last_open_user_agent` - Last opener device
-   `created_at`, `updated_at`

### `subscribers` (nl\_)

Newsletter subscriber records

-   `id` - Primary key
-   `nl_email` - Email address (unique)
-   `nl_name` - Subscriber name
-   `nl_is_active` - Active subscription status
-   `nl_unsubscribe_token` - For unsubscribe links
-   `nl_country_code` - Country for targeting
-   `bd_birth_date` - Birthday (Birthday system)
-   `created_at`, `updated_at`

### `tags` (nl\_)

Newsletter audience tags

-   `id` - Primary key
-   `nl_name` - Tag name
-   `nl_slug` - URL slug
-   `created_at`, `updated_at`

### `subscriber_tag` (nl\_)

Pivot table for subscriber-tag relationships

-   `id` - Primary key
-   `subscriber_id` - Foreign key
-   `tag_id` - Foreign key
-   `created_at`, `updated_at`

---

## GuestLetter System Tables

### `bookings` (gl\_)

Guest booking records

-   `id` - Primary key
-   `gl_guest_id` - Foreign key to guests
-   `gl_booking_number` - Unique booking reference
-   `gl_arrival_date` - Check-in date
-   `gl_departure_date` - Check-out date
-   `gl_adult` - Number of adults
-   `gl_child` - Number of children
-   `gl_room_id` - Foreign key to rooms
-   `gl_campaign_name` - Marketing campaign
-   `gl_campaign_benefit` - Campaign details
-   `gl_remark` - Internal notes
-   `gl_confirmation_sent_at` - When confirmation email sent
-   `gl_reference` - External reference
-   `created_at`, `updated_at`

### `guest_letter_sends` (gl\_)

Tracking individual guest letter sends

-   `id` - Primary key
-   `gl_booking_id` - Foreign key to bookings
-   `gl_type` - Letter type: confirmation, pre_arrival, post_stay
-   `gl_status` - pending, processing, sent, failed
-   `gl_to_email` - Recipient email
-   `gl_scheduled_for` - When to send
-   `gl_sent_at` - When actually sent
-   `gl_failed_at` - When failed
-   `gl_error_message` - Error details
-   `created_at`, `updated_at`

### `letter_schedules` (gl\_)

Template schedule configurations

-   `id` - Primary key
-   `gl_booking_id` - Foreign key (optional)
-   `gl_type` - Letter type
-   `gl_scheduled_for` - Schedule time
-   `created_at`, `updated_at`

### `letter_templates` (gl\_)

Email templates for guest letters

-   `id` - Primary key
-   `gl_name` - Template name
-   `gl_type` - confirmation, pre_arrival, post_stay
-   `gl_content` - Template HTML/text
-   `created_at`, `updated_at`

### `rooms` (gl\_)

Room/accommodation types

-   `id` - Primary key
-   `gl_name` - Room name/number
-   `gl_description` - Room details
-   `gl_capacity` - Max occupants
-   `created_at`, `updated_at`

---

## Birthday System Tables

### `birthday_sends` (bd\_)

Birthday email send tracking

-   `id` - Primary key
-   `bd_subscriber_id` - Foreign key to subscribers
-   `bd_year` - Year of send
-   `bd_sent_at` - When sent
-   `bd_status` - pending, sent, failed
-   `bd_error` - Error message
-   `created_at`, `updated_at`

---

## Shared Tables (Cross-System)

### `guests`

Guest information (used by GuestLetter, referenced by Birthday)

-   `id` - Primary key
-   `title` - Mr., Ms., Dr., etc.
-   `first_name` - First name
-   `last_name` - Last name
-   `email` - Email address
-   `phone` - Phone number
-   `country` - Country
-   `bd_birth_date` - Birthday (Birthday system)
-   `created_at`, `updated_at`

### `reservations`

Reservation tracking (shared)

-   `id` - Primary key
-   Fields specific to reservations system
-   `created_at`, `updated_at`

### `users` (Role-based)

System users and staff

-   `id` - Primary key
-   `name` - User name
-   `email` - User email
-   `email_verified_at`
-   `password` - Hashed password
-   `remember_token`
-   `role` - User role
-   `department` - Department
-   `created_at`, `updated_at`

### `cache`, `jobs`, `migrations`

Laravel system tables (no prefixes)

---

## Query Examples with Prefixes

### Newsletter Queries

```php
// Get active subscribers
Subscriber::where('nl_is_active', true)->get();

// Find newsletter by subject
Newsletter::where('nl_subject', 'like', '%content%')->first();

// Track opens
NewsletterSend::where('nl_open_count', '>', 0)->get();
```

### GuestLetter Queries

```php
// Get upcoming arrivals
Booking::where('gl_arrival_date', '>=', today())->get();

// Find pending letters
GuestLetterSend::where('gl_status', 'pending')->get();

// Get pre-arrival letters only
GuestLetterSend::where('gl_type', 'pre_arrival')->get();
```

### Birthday Queries

```php
// Check if birthday sent this year
BirthdaySend::where('bd_year', now()->year)
    ->where('bd_subscriber_id', $subscriberId)
    ->exists();
```

---

## Index Information

Key indexes created:

-   `subscribers.nl_email` - Unique (for lookups)
-   `bookings.gl_arrival_date` - Indexed (for date ranges)
-   `bookings.gl_departure_date` - Indexed (for date ranges)
-   `guest_letter_sends.gl_booking_id` - Foreign key index
-   `newsletter_sends.nl_newsletter_id` - Foreign key index
-   `birthday_sends.bd_subscriber_id` - Foreign key index

---

## Foreign Key Relationships

### Bookings → Guests

```
bookings.gl_guest_id → guests.id
Action: CASCADE on delete
```

### Bookings → Rooms

```
bookings.gl_room_id → rooms.id
Action: SET NULL on delete
```

### Newsletter_sends → Newsletters

```
newsletter_sends.nl_newsletter_id → newsletters.id
```

### Newsletter_sends → Subscribers

```
newsletter_sends.nl_subscriber_id → subscribers.id
```

### Guest_letter_sends → Bookings

```
guest_letter_sends.gl_booking_id → bookings.id
```

### Birthday_sends → Subscribers

```
birthday_sends.bd_subscriber_id → subscribers.id
```

### Subscriber_tag → Subscribers & Tags

```
subscriber_tag.subscriber_id → subscribers.id
subscriber_tag.tag_id → tags.id
```

---

## Migration History

| Date           | Migration                                         | Status         |
| -------------- | ------------------------------------------------- | -------------- |
| 2025-12-10     | Create subscribers, newsletters, newsletter_sends | ✅ Applied     |
| 2025-12-15     | Add open tracking to newsletter_sends             | ✅ Applied     |
| 2025-12-17     | Create tags, add targeting to newsletters         | ✅ Applied     |
| 2025-12-26     | Create reservations, letter templates, schedules  | ✅ Applied     |
| 2025-12-30     | Create guests, bookings, rooms                    | ✅ Applied     |
| 2026-01-02     | Create birthday_sends                             | ✅ Applied     |
| 2026-01-06     | Create guest_letter_sends                         | ✅ Applied     |
| 2026-01-09     | Add role and department to users                  | ✅ Applied     |
| **2026-01-14** | **Add system prefixes (nl*, gl*, bd\_)**          | **✅ Applied** |

---

## Verification

To verify the schema with prefixes:

```bash
# Check table structure
php artisan tinker
> Schema::getColumnListing('subscribers')
> Schema::getColumnListing('bookings')
> Schema::getColumnListing('guest_letter_sends')
```

---

## Notes for Developers

1. **Always use prefixes** in queries and code to make it clear which system fields belong to
2. **Shared models** (guests, rooms) may have fields from different systems
3. **Foreign keys** are indexed automatically for performance
4. **Timestamps** (`created_at`, `updated_at`) are automatically managed
5. **No prefixes** on Laravel system tables: `users`, `cache`, `jobs`, `migrations`
