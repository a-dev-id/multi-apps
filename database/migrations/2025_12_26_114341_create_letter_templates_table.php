<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('letter_templates', function (Blueprint $table) {
            $table->id();

            $table->string('name')->nullable(); // internal label
            $table->string('type', 50)->index();       // ✅ shorter
            $table->string('language', 10)->default('en')->index(); // ✅ shorter

            $table->string('subject');
            $table->longText('body'); // HTML or Blade-like text

            $table->boolean('is_active')->default(true)->index();

            $table->timestamps();

            $table->unique(['type', 'language']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_templates');
    }
};
