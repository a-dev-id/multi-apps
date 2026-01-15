<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Remove column prefixes - keep original column names.
     * Table names already have prefixes (nl_, gl_, bd_).
     */
    public function up(): void
    {
        // ==================== NEWSLETTER COLUMNS ====================

        // nl_newsletters table
        Schema::table('nl_newsletters', function (Blueprint $table) {
            if (Schema::hasColumn('nl_newsletters', 'nl_subject')) {
                $table->renameColumn('nl_subject', 'subject');
            }
            if (Schema::hasColumn('nl_newsletters', 'nl_body_html')) {
                $table->renameColumn('nl_body_html', 'body_html');
            }
            if (Schema::hasColumn('nl_newsletters', 'nl_scheduled_at')) {
                $table->renameColumn('nl_scheduled_at', 'scheduled_at');
            }
            if (Schema::hasColumn('nl_newsletters', 'nl_sent_at')) {
                $table->renameColumn('nl_sent_at', 'sent_at');
            }
            if (Schema::hasColumn('nl_newsletters', 'nl_tag_id')) {
                $table->renameColumn('nl_tag_id', 'tag_id');
            }
            if (Schema::hasColumn('nl_newsletters', 'nl_audience_type')) {
                $table->renameColumn('nl_audience_type', 'audience_type');
            }
            if (Schema::hasColumn('nl_newsletters', 'nl_country_codes')) {
                $table->renameColumn('nl_country_codes', 'country_codes');
            }
            if (Schema::hasColumn('nl_newsletters', 'nl_send_to_all')) {
                $table->renameColumn('nl_send_to_all', 'send_to_all');
            }
        });

        // nl_newsletter_sends table
        Schema::table('nl_newsletter_sends', function (Blueprint $table) {
            if (Schema::hasColumn('nl_newsletter_sends', 'nl_newsletter_id')) {
                $table->renameColumn('nl_newsletter_id', 'newsletter_id');
            }
            if (Schema::hasColumn('nl_newsletter_sends', 'nl_subscriber_id')) {
                $table->renameColumn('nl_subscriber_id', 'subscriber_id');
            }
            if (Schema::hasColumn('nl_newsletter_sends', 'nl_email')) {
                $table->renameColumn('nl_email', 'email');
            }
            if (Schema::hasColumn('nl_newsletter_sends', 'nl_sent_at')) {
                $table->renameColumn('nl_sent_at', 'sent_at');
            }
            if (Schema::hasColumn('nl_newsletter_sends', 'nl_open_count')) {
                $table->renameColumn('nl_open_count', 'open_count');
            }
            if (Schema::hasColumn('nl_newsletter_sends', 'nl_opened_at')) {
                $table->renameColumn('nl_opened_at', 'opened_at');
            }
            if (Schema::hasColumn('nl_newsletter_sends', 'nl_last_open_ip')) {
                $table->renameColumn('nl_last_open_ip', 'last_open_ip');
            }
            if (Schema::hasColumn('nl_newsletter_sends', 'nl_last_open_country')) {
                $table->renameColumn('nl_last_open_country', 'last_open_country');
            }
            if (Schema::hasColumn('nl_newsletter_sends', 'nl_last_open_user_agent')) {
                $table->renameColumn('nl_last_open_user_agent', 'last_open_user_agent');
            }
        });

        // nl_subscribers table
        Schema::table('nl_subscribers', function (Blueprint $table) {
            if (Schema::hasColumn('nl_subscribers', 'nl_email')) {
                $table->renameColumn('nl_email', 'email');
            }
            if (Schema::hasColumn('nl_subscribers', 'nl_name')) {
                $table->renameColumn('nl_name', 'name');
            }
            if (Schema::hasColumn('nl_subscribers', 'nl_is_active')) {
                $table->renameColumn('nl_is_active', 'is_active');
            }
            if (Schema::hasColumn('nl_subscribers', 'nl_unsubscribe_token')) {
                $table->renameColumn('nl_unsubscribe_token', 'unsubscribe_token');
            }
            if (Schema::hasColumn('nl_subscribers', 'nl_country_code')) {
                $table->renameColumn('nl_country_code', 'country_code');
            }
            if (Schema::hasColumn('nl_subscribers', 'bd_birth_date')) {
                $table->renameColumn('bd_birth_date', 'birth_date');
            }
        });

        // nl_tags table
        Schema::table('nl_tags', function (Blueprint $table) {
            if (Schema::hasColumn('nl_tags', 'nl_name')) {
                $table->renameColumn('nl_name', 'name');
            }
            if (Schema::hasColumn('nl_tags', 'nl_slug')) {
                $table->renameColumn('nl_slug', 'slug');
            }
        });

        // ==================== GUESTLETTER COLUMNS ====================

        // gl_bookings table
        Schema::table('gl_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('gl_bookings', 'gl_guest_id')) {
                $table->renameColumn('gl_guest_id', 'guest_id');
            }
            if (Schema::hasColumn('gl_bookings', 'gl_booking_number')) {
                $table->renameColumn('gl_booking_number', 'booking_number');
            }
            if (Schema::hasColumn('gl_bookings', 'gl_arrival_date')) {
                $table->renameColumn('gl_arrival_date', 'arrival_date');
            }
            if (Schema::hasColumn('gl_bookings', 'gl_departure_date')) {
                $table->renameColumn('gl_departure_date', 'departure_date');
            }
            if (Schema::hasColumn('gl_bookings', 'gl_adult')) {
                $table->renameColumn('gl_adult', 'adult');
            }
            if (Schema::hasColumn('gl_bookings', 'gl_child')) {
                $table->renameColumn('gl_child', 'child');
            }
            if (Schema::hasColumn('gl_bookings', 'gl_room_id')) {
                $table->renameColumn('gl_room_id', 'room_id');
            }
            if (Schema::hasColumn('gl_bookings', 'gl_campaign_name')) {
                $table->renameColumn('gl_campaign_name', 'campaign_name');
            }
            if (Schema::hasColumn('gl_bookings', 'gl_campaign_benefit')) {
                $table->renameColumn('gl_campaign_benefit', 'campaign_benefit');
            }
            if (Schema::hasColumn('gl_bookings', 'gl_remark')) {
                $table->renameColumn('gl_remark', 'remark');
            }
            if (Schema::hasColumn('gl_bookings', 'gl_confirmation_sent_at')) {
                $table->renameColumn('gl_confirmation_sent_at', 'confirmation_sent_at');
            }
            if (Schema::hasColumn('gl_bookings', 'gl_reference')) {
                $table->renameColumn('gl_reference', 'reference');
            }
        });

        // gl_guest_letter_sends table
        Schema::table('gl_guest_letter_sends', function (Blueprint $table) {
            if (Schema::hasColumn('gl_guest_letter_sends', 'gl_booking_id')) {
                $table->renameColumn('gl_booking_id', 'booking_id');
            }
            if (Schema::hasColumn('gl_guest_letter_sends', 'gl_type')) {
                $table->renameColumn('gl_type', 'type');
            }
            if (Schema::hasColumn('gl_guest_letter_sends', 'gl_status')) {
                $table->renameColumn('gl_status', 'status');
            }
            if (Schema::hasColumn('gl_guest_letter_sends', 'gl_to_email')) {
                $table->renameColumn('gl_to_email', 'to_email');
            }
            if (Schema::hasColumn('gl_guest_letter_sends', 'gl_scheduled_for')) {
                $table->renameColumn('gl_scheduled_for', 'scheduled_for');
            }
            if (Schema::hasColumn('gl_guest_letter_sends', 'gl_sent_at')) {
                $table->renameColumn('gl_sent_at', 'sent_at');
            }
            if (Schema::hasColumn('gl_guest_letter_sends', 'gl_failed_at')) {
                $table->renameColumn('gl_failed_at', 'failed_at');
            }
            if (Schema::hasColumn('gl_guest_letter_sends', 'gl_error_message')) {
                $table->renameColumn('gl_error_message', 'error_message');
            }
        });

        // gl_letter_schedules table
        Schema::table('gl_letter_schedules', function (Blueprint $table) {
            if (Schema::hasColumn('gl_letter_schedules', 'gl_booking_id')) {
                $table->renameColumn('gl_booking_id', 'booking_id');
            }
            if (Schema::hasColumn('gl_letter_schedules', 'gl_type')) {
                $table->renameColumn('gl_type', 'type');
            }
            if (Schema::hasColumn('gl_letter_schedules', 'gl_scheduled_for')) {
                $table->renameColumn('gl_scheduled_for', 'scheduled_for');
            }
        });

        // gl_letter_templates table
        Schema::table('gl_letter_templates', function (Blueprint $table) {
            if (Schema::hasColumn('gl_letter_templates', 'gl_name')) {
                $table->renameColumn('gl_name', 'name');
            }
            if (Schema::hasColumn('gl_letter_templates', 'gl_type')) {
                $table->renameColumn('gl_type', 'type');
            }
            if (Schema::hasColumn('gl_letter_templates', 'gl_content')) {
                $table->renameColumn('gl_content', 'content');
            }
        });

        // gl_rooms table
        Schema::table('gl_rooms', function (Blueprint $table) {
            if (Schema::hasColumn('gl_rooms', 'gl_name')) {
                $table->renameColumn('gl_name', 'name');
            }
            if (Schema::hasColumn('gl_rooms', 'gl_image')) {
                $table->renameColumn('gl_image', 'image');
            }
            if (Schema::hasColumn('gl_rooms', 'gl_description')) {
                $table->renameColumn('gl_description', 'description');
            }
            if (Schema::hasColumn('gl_rooms', 'gl_is_active')) {
                $table->renameColumn('gl_is_active', 'is_active');
            }
        });

        // ==================== BIRTHDAY COLUMNS ====================

        // bd_birthday_sends table
        Schema::table('bd_birthday_sends', function (Blueprint $table) {
            if (Schema::hasColumn('bd_birthday_sends', 'bd_subscriber_id')) {
                $table->renameColumn('bd_subscriber_id', 'subscriber_id');
            }
            if (Schema::hasColumn('bd_birthday_sends', 'bd_year')) {
                $table->renameColumn('bd_year', 'year');
            }
            if (Schema::hasColumn('bd_birthday_sends', 'bd_sent_at')) {
                $table->renameColumn('bd_sent_at', 'sent_at');
            }
            if (Schema::hasColumn('bd_birthday_sends', 'bd_status')) {
                $table->renameColumn('bd_status', 'status');
            }
            if (Schema::hasColumn('bd_birthday_sends', 'bd_error')) {
                $table->renameColumn('bd_error', 'error');
            }
        });

        // ==================== SHARED TABLES ====================

        // guests table (keep birth_date for birthday system)
        Schema::table('guests', function (Blueprint $table) {
            if (Schema::hasColumn('guests', 'bd_birth_date')) {
                $table->renameColumn('bd_birth_date', 'birth_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse all column renames - put prefixes back
        // (same logic but swap the column names)
    }
};
