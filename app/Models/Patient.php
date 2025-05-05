<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'dob'
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
