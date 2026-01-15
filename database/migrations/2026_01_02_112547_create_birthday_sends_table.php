<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_birthday_sends_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('birthday_sends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscriber_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->timestamp('sent_at')->nullable();
            $table->string('status')->default('sent'); // sent|failed
            $table->text('error')->nullable();
            $table->timestamps();

            $table->unique(['subscriber_id', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('birthday_sends');
    }
};
