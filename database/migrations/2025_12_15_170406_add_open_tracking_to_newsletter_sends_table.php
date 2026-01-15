<?php

// database/migrations/xxxx_add_open_tracking_to_newsletter_sends_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('newsletter_sends', function (Blueprint $table) {
            $table->dateTime('opened_at')->nullable()->after('sent_at');
            $table->unsignedInteger('open_count')->default(0)->after('opened_at');
            $table->string('last_open_ip', 45)->nullable()->after('open_count');
            $table->string('last_open_country', 2)->nullable()->after('last_open_ip'); // ISO2
            $table->text('last_open_user_agent')->nullable()->after('last_open_country');

            $table->index(['newsletter_id', 'opened_at']);
            $table->index(['newsletter_id', 'last_open_country']);
        });
    }

    public function down(): void
    {
        Schema::table('newsletter_sends', function (Blueprint $table) {
            $table->dropIndex(['newsletter_id', 'opened_at']);
            $table->dropIndex(['newsletter_id', 'last_open_country']);

            $table->dropColumn([
                'opened_at',
                'open_count',
                'last_open_ip',
                'last_open_country',
                'last_open_user_agent',
            ]);
        });
    }
};
