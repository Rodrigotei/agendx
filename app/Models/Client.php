<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'name',
        'document',
        'email',
        'phone'
    ];
    
    use HasFactory;
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
