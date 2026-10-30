<?php

namespace App\Models;

use Database\Factories\AvailableDateFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['date', 'max_visits', 'notes'])]
class AvailableDate extends Model
{
    /** @use HasFactory<AvailableDateFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }
}
