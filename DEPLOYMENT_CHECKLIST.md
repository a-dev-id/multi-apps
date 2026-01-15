# Deployment Checklist - January 15, 2026

## Pre-Deployment Verification ✅

### Code Quality

-   [x] No PHP compilation errors
-   [x] All imports are correct
-   [x] No missing dependencies
-   [x] Code follows Laravel/Filament conventions

### Database

-   [x] Migration created: `2026_01_15_123448_add_guest_id_to_guest_letter_sends_table.php`
-   [x] Migration successfully applied locally
-   [x] `guest_id` column added to `gl_guest_letter_sends` table
-   [x] Foreign key constraint added to `guests` table

### Key Files Modified

1. **Models**

    - [x] `GuestLetterSend::guestDirect()` relationship added
    - [x] `guest_id` added to `$fillable` array

2. **Mail Classes**

    - [x] `PostStayLetterMail` - Guest name included in subject for manually created letters
    - [x] Email subject personalization for both booking-based and direct guest letters

3. **Jobs**

    - [x] `SendGuestLetterJob` - Uses `guestDirect` relationship instead of `guest`

4. **Admin Pages (Filament)**

    - [x] `PostStayLetterResource` - Added model labels for better page title
    - [x] `PostStayLettersTable` - Filters only `type = 'post_stay'`, displays guest name correctly
    - [x] `PostStayLetterForm` - Shows guest details when guest is selected (read-only)
    - [x] `CreatePostStayLetter` - Cleans up display-only fields before saving
    - [x] `ListSubscribers` - Guest import action added with ActionGroup dropdown

5. **Forms**
    - [x] `PostStayLetterForm` - Guest selection shows only guests without pending/sent post-stay letters

## Features Verified

### Post-Stay Letter System

-   [x] Can create post-stay letters manually (without booking)
-   [x] Guest email automatically populated from selected guest
-   [x] Guest name appears in email subject and body
-   [x] Table shows only post-stay letters (filtered by type)
-   [x] Guest names display correctly (fallback for both booking and direct guests)

### Newsletter Subscriber Import

-   [x] New "Tools" dropdown menu with organized actions
-   [x] Import Guests from Letter System button
    -   Checks for duplicate emails
    -   Only imports new guests
    -   Shows import statistics
-   [x] All 4 actions have tooltips explaining functionality
    -   Import Guests from Letter System
    -   Import Subscribers from CSV
    -   Check & Merge Duplicates
    -   Purge Bounced CSV

## Shared Hosting Deployment Steps

### 1. Database Setup

```bash
# Run migrations (includes new guest_id column)
php artisan migrate
```

### 2. Environment Configuration

-   Ensure `.env` file is configured for shared hosting
-   Set `APP_ENV=production`
-   Set `APP_DEBUG=false`
-   Configure database credentials
-   Set queue driver (database or other)

### 3. Queue Processing

```bash
# For shared hosting, use schedule:work or set up supervisor
php artisan queue:work --stop-when-empty --max-jobs=20 --max-time=50

# Or use scheduled command
php artisan schedule:work
```

### 4. Verification Checklist

-   [x] No compile errors
-   [x] Database migrations successful
-   [x] All relationships work correctly
-   [x] Email personalization works
-   [x] Guest import functionality works
-   [x] UI optimizations applied

## Production Notes

### Important Configuration

-   **Queue Driver**: Database-backed queue (ensure `jobs` table exists)
-   **Cron/Scheduler**: Run `php artisan schedule:run` or `schedule:work` regularly
-   **Email**: Configure mail driver in `.env` (currently set to `log` for testing)
-   **Cache**: Ensure cache and session drivers are configured

### Known Limitations

-   Guest import uses email as unique identifier - duplicates are skipped
-   Guest name personalization requires complete guest data (title, first_name, last_name)
-   Dropdown menu may be wide on very small screens (mobile optimization possible)

### Shared Hosting Specific

-   If supervisor is not available, use `php artisan schedule:work` in background
-   Monitor queue table size periodically to prevent excessive growth
-   Set up logs rotation to prevent disk space issues

## Rollback Plan (If Needed)

If issues occur on production:

1. **Database Rollback**

    ```bash
    php artisan migrate:rollback --step=1
    ```

    This will remove the `guest_id` column

2. **Code Rollback**
    - Revert to previous commit that didn't include the guest_id changes
    - Main changes are backward compatible (guest_id is nullable)

## Documentation Updated

-   [x] `.github/copilot-instructions.md` updated with all new features
-   [x] Database schema changes documented
-   [x] UI/UX improvements documented
-   [x] Recent updates section added with dates

## Sign-Off

-   Code Review: ✅ Complete
-   Testing: ✅ No errors found
-   Documentation: ✅ Updated
-   Ready for Deployment: ✅ Yes

---

**Last Updated**: January 15, 2026
**Status**: Ready for Shared Hosting Deployment
