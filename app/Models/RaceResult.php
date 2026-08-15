<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RaceResult extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'approved_at' => 'datetime',
        ];
    }
}
