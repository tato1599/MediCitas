<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'name',
        'start',
        'duration',
        'type',
        'color',
    ];

    public function user()
    {
        return $this->belongsToMany(User::class, 'schedule_user', 'schedule_id', 'user_id')
            ->withPivot(['id', 'start_date', 'end_date', 'rrule']);
    }

    public function scopeMeals()
    {

    }
}
