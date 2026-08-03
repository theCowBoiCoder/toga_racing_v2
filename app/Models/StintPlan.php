<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StintPlan extends Model
{
    protected $fillable = ['token', 'availability_token', 'plan'];

    protected function casts(): array
    {
        return ['plan' => 'array'];
    }
}
