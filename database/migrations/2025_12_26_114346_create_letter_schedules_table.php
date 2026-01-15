<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('letter_schedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();

            // confirmation | pre_arrival | post_stay
            $table->string('type', 30)->index();

            // pending | sent | failed | cancelled
            $table->string('status', 20)->default('pending')->index();

            $table->dateTime('scheduled_at')->nullable()->index();
            $table->dateTime('sent_at')->nullable()->index();

            $table->text('error_message')->nullable();

            $table->timestamps();

            $table->unique(['booking_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_schedules');
    }
};
