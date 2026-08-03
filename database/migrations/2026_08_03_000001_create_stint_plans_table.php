<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stint_plans', function (Blueprint $table) {
            $table->id();
            $table->uuid('token')->unique();
            $table->json('plan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stint_plans');
    }
};
