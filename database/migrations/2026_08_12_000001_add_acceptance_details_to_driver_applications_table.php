<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('driver_applications', function (Blueprint $table) {
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('welcome_email_sent_at')->nullable();
            $table->string('accepted_by_discord_id')->nullable();
            $table->string('accepted_by_discord_name')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('driver_applications', function (Blueprint $table) {
            $table->dropColumn([
                'accepted_at',
                'welcome_email_sent_at',
                'accepted_by_discord_id',
                'accepted_by_discord_name',
            ]);
        });
    }
};
