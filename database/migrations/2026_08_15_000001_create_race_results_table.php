<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('race_results', function (Blueprint $table) {
            $table->id();
            $table->string('event_name', 180);
            $table->string('simulator', 30);
            $table->unsignedSmallInteger('split_number');
            $table->unsignedSmallInteger('starting_position');
            $table->unsignedSmallInteger('finishing_position');
            $table->string('car_class', 100);
            $table->text('team_members');
            $table->string('image_path');
            $table->string('image_original_name')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->timestamp('approved_at')->nullable();
            $table->string('approved_by_discord_id')->nullable();
            $table->string('approved_by_discord_name')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('race_results');
    }
};
