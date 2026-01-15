<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('newsletters', function (Blueprint $table) {
            $table->string('audience_type')->default('all')->after('scheduled_at'); // all|country|year
            $table->json('country_codes')->nullable()->after('audience_type');
            $table->unsignedSmallInteger('guest_year')->nullable()->after('country_codes');
        });
    }

    public function down(): void
    {
        Schema::table('newsletters', function (Blueprint $table) {
            $table->dropColumn(['audience_type', 'country_codes', 'guest_year']);
        });
    }
};
