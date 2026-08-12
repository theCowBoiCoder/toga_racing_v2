<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverApplication extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'simulators' => 'array',
            'accepted_at' => 'datetime',
            'welcome_email_sent_at' => 'datetime',
        ];
    }
}
