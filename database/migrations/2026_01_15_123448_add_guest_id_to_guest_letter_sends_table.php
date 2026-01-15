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
        Schema::table('gl_guest_letter_sends', function (Blueprint $table) {
            $table->unsignedBigInteger('guest_id')->nullable()->after('booking_id');
            $table->foreign('guest_id')->references('id')->on('guests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gl_guest_letter_sends', function (Blueprint $table) {
            $table->dropForeign(['guest_id']);
            $table->dropColumn('guest_id');
        });
    }
};
