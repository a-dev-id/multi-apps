<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            $table->date('birth_date')->nullable()->after('email');
            $table->string('country_code', 5)->nullable()->after('birth_date'); // CHN, USA, DEU, UKR
        });
    }

    public function down(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            $table->dropColumn(['birth_date', 'country_code']);
        });
    }
};
