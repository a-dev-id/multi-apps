<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('guest_id')->constrained()->cascadeOnDelete();

            $table->string('booking_number', 80)->unique();

            $table->date('arrival_date')->index();
            $table->date('departure_date')->index();

            $table->unsignedTinyInteger('adult')->default(2);
            $table->unsignedTinyInteger('child')->default(0);

            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();

            $table->string('campaign_name')->nullable();
            $table->longText('campaign_benefit')->nullable();
            $table->longText('remark')->nullable();

            $table->timestamp('confirmation_sent_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
