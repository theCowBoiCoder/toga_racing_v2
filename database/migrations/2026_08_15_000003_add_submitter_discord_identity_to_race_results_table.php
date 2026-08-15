<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('race_results', function (Blueprint $table) {
            $table->string('submitter_discord_id')->nullable()->after('image_original_name');
            $table->string('submitter_discord_username')->nullable()->after('submitter_discord_id');
            $table->string('submitter_discord_display_name')->nullable()->after('submitter_discord_username');
        });
    }

    public function down(): void
    {
        Schema::table('race_results', function (Blueprint $table) {
            $table->dropColumn([
                'submitter_discord_id',
                'submitter_discord_username',
                'submitter_discord_display_name',
            ]);
        });
    }
};
