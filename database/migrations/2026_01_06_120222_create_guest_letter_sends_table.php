<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guest_letter_sends', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();

            // confirmation | pre_arrival | post_stay
            $table->string('type', 32);

            // pending | sent | failed | cancelled
            $table->string('status', 24)->default('pending');

            $table->string('to_email', 191);

            $table->dateTime('scheduled_for')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->dateTime('failed_at')->nullable();

            $table->text('error_message')->nullable();

            $table->timestamps();

            $table->unique(['booking_id', 'type']);
            $table->index(['status', 'scheduled_for']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_letter_sends');
    }
};
