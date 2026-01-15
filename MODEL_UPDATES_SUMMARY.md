# Model Updates Summary - Column Prefix Migration

**Date:** January 14, 2026  
**Status:** ✅ Complete  
**Database Prefixes Applied:** nl* (Newsletter), gl* (GuestLetter), bd\_ (Birthday)

---

## Overview

All model accessors, job handlers, controllers, and routes have been updated to reference the new prefixed column names that were applied via database migration `2026_01_14_000001_add_system_prefixes_refactor.php`.

---

## Files Updated (17 Total)

### Newsletter Module (6 files)

#### 1. **Models/Newsletter.php**

-   Added `$fillable` array with: `nl_subject`, `nl_body_html`, `nl_scheduled_at`, `nl_sent_at`, `nl_tag_id`, `nl_audience_type`, `nl_country_codes`, `nl_send_to_all`
-   Added `$casts` with datetime and boolean casts
-   Updated `sends()` relationship to use `nl_newsletter_id` foreign key

#### 2. **Models/Subscriber.php**

-   Added `$fillable` array with: `nl_email`, `nl_name`, `nl_is_active`, `nl_unsubscribe_token`, `nl_country_code`, `bd_birth_date`
-   Added `$casts` for `nl_is_active` (boolean) and `bd_birth_date` (date)
-   Updated `booted()` to set `nl_unsubscribe_token`

#### 3. **Models/NewsletterSend.php**

-   Updated `$fillable` to use: `nl_newsletter_id`, `nl_subscriber_id`, `nl_email`, `nl_sent_at`, `nl_open_count`, `nl_opened_at`, `nl_last_open_ip`, `nl_last_open_country`, `nl_last_open_user_agent`
-   Added `$casts` for datetime fields

#### 4. **Models/Tag.php**

-   Updated `$fillable` to: `nl_name`, `nl_slug`
-   Updated `booted()` to auto-set `nl_slug` from `nl_name`

#### 5. **Jobs/SendNewsletterJob.php**

-   Updated `handle()` to check `nl_is_active` on subscriber
-   Updated `firstOrCreate` to use `nl_newsletter_id`, `nl_subscriber_id` keys
-   Updated `sent_at` logic to use `nl_sent_at`
-   Updated `Mail::to()` to use `$subscriber->nl_email`

#### 6. **Mail/NewsletterMail.php**

-   Updated `envelope()` to use `$this->newsletter->nl_subject`
-   Updated `content()` to pass correct subject property reference

### GuestLetter Module (5 files)

#### 7. **Models/Booking.php**

-   Updated `$fillable` to use all `gl_` prefixes: `gl_guest_id`, `gl_room_id`, `gl_booking_number`, `gl_arrival_date`, `gl_departure_date`, `gl_adult`, `gl_child`, `gl_campaign_name`, `gl_campaign_benefit`, `gl_remark`, `gl_confirmation_sent_at`, `gl_reference`
-   Updated `$casts` for datetime fields with `gl_` prefix
-   Updated relationships: `guest()`, `room()`, `letterSchedules()`, `guestLetterSends()` to use prefixed foreign keys

#### 8. **Models/GuestLetterSend.php**

-   Updated `$fillable` to use all `gl_` prefixes
-   Updated `$casts` for datetime fields
-   Updated `booking()` relationship to use `gl_booking_id` foreign key

#### 9. **Models/LetterTemplate.php**

-   Added `$fillable` array with: `gl_name`, `gl_type`, `gl_content`

#### 10. **Models/LetterSchedule.php**

-   Updated `$fillable` to use: `gl_booking_id`, `gl_type`, `gl_scheduled_for`
-   Added `$casts` for `gl_scheduled_for` (datetime)

#### 11. **Observers/BookingObserver.php**

-   Updated `created()` method to use `gl_` prefixed columns:
    -   `gl_booking_id`, `gl_type` in queries
    -   `gl_status`, `gl_to_email`, `gl_scheduled_for` in creates
    -   `gl_arrival_date`, `gl_departure_date` in date calculations

#### 12. **Console/Commands/DispatchDueGuestLetters.php**

-   Updated query to use `gl_status`, `gl_scheduled_for` in where clauses
-   Updated orderBy to use `gl_scheduled_for`

#### 13. **Jobs/SendGuestLetterJob.php**

-   Updated all property checks to use `gl_` prefixes: `gl_status`, `gl_to_email`, `gl_type`
-   Updated all updates to use prefixed columns
-   Updated match statement to check `gl_type`
-   Updated booking confirmation to use `gl_confirmation_sent_at`

### Birthday Module (1 file)

#### 14. **Models/BirthdaySend.php**

-   Updated `$fillable` to use: `bd_subscriber_id`, `bd_year`, `bd_sent_at`, `bd_status`, `bd_error`
-   Added `$casts` for `bd_sent_at` (datetime)

#### 15. **Jobs/SendBirthdayEmailJob.php**

-   Updated to check `nl_is_active` on subscriber
-   Updated `firstOrCreate` to use `bd_subscriber_id`, `bd_year` keys
-   Updated all updates to use `bd_` prefixes
-   Updated `Mail::to()` to use `$subscriber->nl_email`

### Shared Models (1 file)

#### 16. **Models/Room.php** (in `app/Models/`)

-   Updated `$fillable` to use `gl_` prefixes: `gl_name`, `gl_image`, `gl_description`, `gl_is_active`
-   Added `$casts` for `gl_is_active` (boolean)

#### 17. **Models/Guest.php** (in `app/Models/`)

-   Updated `$fillable` to use `bd_birth_date` instead of `birth_date`
-   Updated `$casts` to use `bd_birth_date`

---

## HTTP Controllers Updated (6 files)

#### 1. **NewsletterController.php**

-   Updated `unsubscribe()` to query by `nl_unsubscribe_token`
-   Updated to check/set `nl_is_active` property

#### 2. **NewsletterPreviewController.php**

-   Updated `show()` to create subscriber with `nl_` prefixed properties
-   Updated to use `nl_unsubscribe_token`

#### 3. **CronNewsletterController.php**

-   Updated queries to use `nl_scheduled_at`, `nl_sent_at`
-   Updated to check `nl_is_active`
-   Updated audience targeting to use: `nl_audience_type`, `nl_country_codes`, `nl_country_code`, `nl_tag_ids`, `nl_slug`
-   Updated final update to use `nl_sent_at`

#### 4. **CronGuestLetterController.php**

-   Updated all query conditions to use `gl_status`, `gl_scheduled_for`
-   Updated orderByRaw to use `gl_scheduled_for`
-   Updated status checks to use `gl_status`
-   Updated update calls to use `gl_` prefixes
-   Updated error message collection to use `gl_type`, `gl_error_message`

#### 5. **CronBirthdayController.php**

-   Updated to check `nl_is_active`
-   Updated to query `bd_birth_date` for month/day
-   Updated all property references to use prefixes

### Routes & Other (2 files)

#### 6. **routes/web.php**

-   Updated newsletter open tracking route to use all `nl_` prefixed columns
-   Updated comments to reference `nl_country_code`

#### 7. **Livewire/GuestLetterSendsTable.php**

-   Updated query to use `gl_booking_id`
-   Updated orderByRaw to use `gl_scheduled_for`

---

## Testing Checklist

-   ✅ PHP syntax validation passed
-   ⚠️ **Pending:** Manual testing of:
    -   [ ] Newsletter campaign creation and sending
    -   [ ] Guest letter dispatch and email sending
    -   [ ] Birthday email scheduling
    -   [ ] Filament admin panel form submission
    -   [ ] Newsletter unsubscribe link functionality
    -   [ ] Cron endpoints (HTTP and CLI)

---

## Key Changes by System

### Newsletter (nl\_)

| Old Column           | New Column              |
| -------------------- | ----------------------- |
| subject              | nl_subject              |
| body_html            | nl_body_html            |
| scheduled_at         | nl_scheduled_at         |
| sent_at              | nl_sent_at              |
| email                | nl_email                |
| name                 | nl_name                 |
| is_active            | nl_is_active            |
| unsubscribe_token    | nl_unsubscribe_token    |
| country_code         | nl_country_code         |
| open_count           | nl_open_count           |
| opened_at            | nl_opened_at            |
| last_open_ip         | nl_last_open_ip         |
| last_open_country    | nl_last_open_country    |
| last_open_user_agent | nl_last_open_user_agent |
| name (tags)          | nl_name                 |
| slug (tags)          | nl_slug                 |
| newsletter_id (fk)   | nl_newsletter_id        |
| subscriber_id (fk)   | nl_subscriber_id        |
| tag_ids              | nl_tag_ids              |
| audience_type        | nl_audience_type        |
| country_codes        | nl_country_codes        |

### GuestLetter (gl\_)

| Old Column           | New Column              |
| -------------------- | ----------------------- |
| guest_id             | gl_guest_id             |
| room_id              | gl_room_id              |
| booking_number       | gl_booking_number       |
| arrival_date         | gl_arrival_date         |
| departure_date       | gl_departure_date       |
| adult                | gl_adult                |
| child                | gl_child                |
| campaign_name        | gl_campaign_name        |
| campaign_benefit     | gl_campaign_benefit     |
| remark               | gl_remark               |
| confirmation_sent_at | gl_confirmation_sent_at |
| reference            | gl_reference            |
| type (letters)       | gl_type                 |
| status (letters)     | gl_status               |
| to_email             | gl_to_email             |
| scheduled_for        | gl_scheduled_for        |
| sent_at              | gl_sent_at              |
| failed_at            | gl_failed_at            |
| error_message        | gl_error_message        |
| booking_id (fk)      | gl_booking_id           |
| name (rooms)         | gl_name                 |
| image (rooms)        | gl_image                |
| description (rooms)  | gl_description          |
| is_active (rooms)    | gl_is_active            |
| name (templates)     | gl_name                 |
| content (templates)  | gl_content              |

### Birthday (bd\_)

| Old Column       | New Column       |
| ---------------- | ---------------- |
| birth_date (all) | bd_birth_date    |
| subscriber_id    | bd_subscriber_id |
| year             | bd_year          |
| sent_at          | bd_sent_at       |
| status           | bd_status        |
| error            | bd_error         |

---

## Notes

1. **Shared Models:** Guest and Room are shared but now prefixed columns. Update any other code that accesses these models.
2. **Relationships:** All belongsTo and hasMany relationships now specify the prefixed foreign key explicitly.
3. **Database Consistency:** All code changes are now synchronized with the database schema applied by migration 2026_01_14_000001.
4. **No Breaking Changes to Public API:** Routes remain the same, only internal column references changed.

---

## Next Steps

1. Run comprehensive tests on all email workflows
2. Test Filament admin panel forms (ensure forms reference prefixed columns)
3. Monitor queue processing to verify jobs work correctly
4. Update email template views if they reference model properties directly
5. Update any raw SQL queries that may exist elsewhere in the codebase
