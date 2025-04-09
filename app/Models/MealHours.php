<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealHours extends Model
{
    protected $fillable = [
        'user_id',
        'start',
        'day',
        'created_by',
        'updated_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
