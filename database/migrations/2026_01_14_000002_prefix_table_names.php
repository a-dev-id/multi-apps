<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rename tables with system prefixes to identify which system owns them.
     * 
     * Prefix Convention:
     * - nl_ : Newsletter (campaigns, subscribers, sends)
     * - gl_ : GuestLetter (bookings, guest letters, rooms)
     * - bd_ : Birthday (birthday emails)
     */
    public function up(): void
    {
        // ==================== NEWSLETTER TABLES ====================
        Schema::rename('newsletters', 'nl_newsletters');
        Schema::rename('newsletter_sends', 'nl_newsletter_sends');
        Schema::rename('subscribers', 'nl_subscribers');
        Schema::rename('tags', 'nl_tags');
        Schema::rename('subscriber_tag', 'nl_subscriber_tag');

        // ==================== GUESTLETTER TABLES ====================
        Schema::rename('bookings', 'gl_bookings');
        Schema::rename('guest_letter_sends', 'gl_guest_letter_sends');
        Schema::rename('letter_schedules', 'gl_letter_schedules');
        Schema::rename('letter_templates', 'gl_letter_templates');
        Schema::rename('rooms', 'gl_rooms');

        // ==================== BIRTHDAY TABLES ====================
        Schema::rename('birthday_sends', 'bd_birthday_sends');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ==================== BIRTHDAY TABLES ====================
        Schema::rename('bd_birthday_sends', 'birthday_sends');

        // ==================== GUESTLETTER TABLES ====================
        Schema::rename('gl_rooms', 'rooms');
        Schema::rename('gl_letter_templates', 'letter_templates');
        Schema::rename('gl_letter_schedules', 'letter_schedules');
        Schema::rename('gl_guest_letter_sends', 'guest_letter_sends');
        Schema::rename('gl_bookings', 'bookings');

        // ==================== NEWSLETTER TABLES ====================
        Schema::rename('nl_subscriber_tag', 'subscriber_tag');
        Schema::rename('nl_tags', 'tags');
        Schema::rename('nl_subscribers', 'subscribers');
        Schema::rename('nl_newsletter_sends', 'newsletter_sends');
        Schema::rename('nl_newsletters', 'newsletters');
    }
};
