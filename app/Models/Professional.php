<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Professional extends Model
{

    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'specialization',
    ];

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
    public function availabilities()
    {
        return $this->hasMany(Availability::class);
    }
}
