<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();

            // Guest
            $table->string('guest_first_name')->nullable();
            $table->string('guest_last_name')->nullable();
            $table->string('guest_email')->index();

            // Stay dates
            $table->date('arrival_date')->index();
            $table->date('checkout_date')->index();

            // Optional identifiers
            $table->string('reservation_code')->nullable()->unique(); // PMS/booking ref
            $table->string('property')->nullable(); // Nandini, HGOB, etc.

            // Status
            $table->string('status')->default('booked')->index(); // booked, cancelled, checked_in, checked_out

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
