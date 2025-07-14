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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('comment');
            $table->enum('type', [
                'high_price',
                'wrong_content',
                'duplicate_post',
                'offensive_content',
                'fraud',
                'other'
            ]);
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('house_id')->constrained('houses');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};