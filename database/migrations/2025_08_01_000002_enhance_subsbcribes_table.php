<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subsbcribes', function (Blueprint $table) {
            // Subscription plan tier sold to real-estate offices.
            $table->string('plan')->default('basic')->after('office_id');
            $table->decimal('price', 10, 2)->default(0)->after('plan');
            // Max active listings allowed by the plan (null = unlimited).
            $table->integer('listing_limit')->nullable()->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('subsbcribes', function (Blueprint $table) {
            $table->dropColumn(['plan', 'price', 'listing_limit']);
        });
    }
};
