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
        Schema::create('guests', function (Blueprint $table) {
            $table->id();

            $table->string('title', 20)->nullable(); // Mr, Mrs, Miss, Ms, Other
            $table->string('first_name');
            $table->string('last_name')->nullable();

            $table->string('email')->index();
            $table->string('phone', 50)->nullable();

            $table->string('country', 2)->nullable(); // ISO2
            $table->date('birth_date')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
