<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('houses', function (Blueprint $table) {
            $table->string('title')->nullable()->after('district_id');
            $table->text('description')->nullable()->after('title');
            $table->string('cover_image')->nullable()->after('description');
            $table->boolean('featured')->default(false)->after('cover_image');
            // When a sale/rent deal closes, we stamp it to power market analytics.
            $table->timestamp('closed_at')->nullable()->after('featured');
        });
    }

    public function down(): void
    {
        Schema::table('houses', function (Blueprint $table) {
            $table->dropColumn(['title', 'description', 'cover_image', 'featured', 'closed_at']);
        });
    }
};
