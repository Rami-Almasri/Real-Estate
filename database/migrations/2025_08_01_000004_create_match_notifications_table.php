<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A match notification is produced when a new house satisfies a buyer's
     * standing preference. One row per (buyer, house, preference).
     */
    public function up(): void
    {
        Schema::create('match_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('house_id')->constrained('houses')->onDelete('cascade');
            $table->foreignId('preference_id')->constrained('preferences')->onDelete('cascade');
            $table->unsignedTinyInteger('score')->default(100);
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'house_id', 'preference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_notifications');
    }
};
