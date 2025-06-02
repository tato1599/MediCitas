<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'dob', 'team_id',
    ];


    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function team()
{
    return $this->belongsTo(Team::class);
}

}
