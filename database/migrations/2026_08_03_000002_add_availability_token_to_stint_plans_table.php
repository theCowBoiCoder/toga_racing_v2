<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stint_plans', function (Blueprint $table) {
            $table->uuid('availability_token')->nullable()->unique()->after('token');
        });
    }

    public function down(): void
    {
        Schema::table('stint_plans', function (Blueprint $table) {
            $table->dropUnique(['availability_token']);
            $table->dropColumn('availability_token');
        });
    }
};
