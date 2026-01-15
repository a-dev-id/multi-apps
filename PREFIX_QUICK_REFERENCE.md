# Column Prefix Quick Reference

## Use This When Accessing Model Properties

### Newsletter System (nl\_)

```php
// Subscriber
$sub->nl_email
$sub->nl_name
$sub->nl_is_active
$sub->nl_unsubscribe_token
$sub->nl_country_code
$sub->bd_birth_date  // Birthday system

// Newsletter
$newsletter->nl_subject
$newsletter->nl_body_html
$newsletter->nl_scheduled_at
$newsletter->nl_sent_at
$newsletter->nl_audience_type
$newsletter->nl_country_codes
$newsletter->nl_tag_ids

// NewsletterSend
$send->nl_newsletter_id
$send->nl_subscriber_id
$send->nl_email
$send->nl_sent_at
$send->nl_open_count
$send->nl_opened_at
$send->nl_last_open_ip
$send->nl_last_open_country
$send->nl_last_open_user_agent

// Tag
$tag->nl_name
$tag->nl_slug
```

### GuestLetter System (gl\_)

```php
// Booking
$booking->gl_guest_id
$booking->gl_room_id
$booking->gl_booking_number
$booking->gl_arrival_date
$booking->gl_departure_date
$booking->gl_adult
$booking->gl_child
$booking->gl_campaign_name
$booking->gl_campaign_benefit
$booking->gl_remark
$booking->gl_confirmation_sent_at
$booking->gl_reference

// GuestLetterSend
$send->gl_booking_id
$send->gl_type  // 'confirmation', 'pre_arrival', 'post_stay'
$send->gl_status  // 'pending', 'sent', 'failed'
$send->gl_to_email
$send->gl_scheduled_for
$send->gl_sent_at
$send->gl_failed_at
$send->gl_error_message

// Room
$room->gl_name
$room->gl_image
$room->gl_description
$room->gl_is_active

// LetterTemplate
$template->gl_name
$template->gl_type
$template->gl_content

// LetterSchedule
$schedule->gl_booking_id
$schedule->gl_type
$schedule->gl_scheduled_for
```

### Birthday System (bd\_)

```php
// BirthdaySend
$send->bd_subscriber_id
$send->bd_year
$send->bd_sent_at
$send->bd_status
$send->bd_error

// Guest (shared model)
$guest->bd_birth_date
```

### Shared Models (No Prefix)

```php
// Guest (other fields)
$guest->title
$guest->first_name
$guest->last_name
$guest->email
$guest->phone
$guest->country

// User
$user->name
$user->email
// ... (standard fields)
```

---

## Database Query Examples

### Find by prefixed column

```php
// Newsletter
Subscriber::where('nl_email', $email)->first();
Newsletter::where('nl_subject', 'like', '%holiday%')->get();

// GuestLetter
Booking::where('gl_booking_number', $number)->first();
GuestLetterSend::where('gl_status', 'pending')->get();

// Birthday
BirthdaySend::where('bd_year', now()->year)->get();
```

### Order by prefixed column

```php
// Order by scheduled time (GL)
$sends->orderBy('gl_scheduled_for');

// Order by sent date (NL)
$newsletters->orderBy('nl_sent_at', 'desc');
```

### Update prefixed columns

```php
// Mark newsletter as sent
$send->update(['nl_sent_at' => now()]);

// Mark letter as failed
$letterSend->update([
    'gl_status' => 'failed',
    'gl_failed_at' => now(),
    'gl_error_message' => 'Invalid email',
]);
```

---

## Important: When Creating Records

```php
// Newsletter Subscriber
Subscriber::create([
    'nl_email' => $email,
    'nl_name' => $name,
    'nl_is_active' => true,
    'nl_country_code' => 'US',
    'bd_birth_date' => $date,
]);

// Guest Letter Send
GuestLetterSend::create([
    'gl_booking_id' => $booking->id,
    'gl_type' => 'confirmation',
    'gl_status' => 'pending',
    'gl_to_email' => $email,
    'gl_scheduled_for' => null,
]);

// Birthday Send
BirthdaySend::create([
    'bd_subscriber_id' => $sub->id,
    'bd_year' => now()->year,
    'bd_status' => 'pending',
]);
```

---

## Migration Complete ✅

All 17 files have been updated to use the system prefixes. This ensures:

-   Clear identification of which system owns each field
-   Prevents accidental cross-system field access
-   Makes codebase intent more explicit
-   Aligns database schema with code organization
