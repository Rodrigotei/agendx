<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Availability extends Model
{
    use HasFactory;    

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }

    protected $fillable = [
        'professional_id',
        'date',
        'start_time',
        'end_time',
        'duration_minutes',
        'is_active',
    ];
}
