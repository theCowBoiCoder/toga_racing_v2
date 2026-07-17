<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('driver_applications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('country');
            $table->string('timezone');
            $table->unsignedTinyInteger('age')->nullable();
            $table->string('discord');
            $table->json('simulators');
            $table->string('car_class');
            $table->text('experience');
            $table->text('availability');
            $table->text('motivation');
            $table->text('profile_links')->nullable();
            $table->string('status')->default('new');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_applications');
    }
};
