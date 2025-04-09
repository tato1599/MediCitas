<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class ScheduleException extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'start',
        'duration',
    ];

    public function start(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return Carbon::parse($value)->format('H:i');
            },
        );
    }

    public function duration(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return Carbon::parse($value)->format('H:i');
            },
        );
    }

    public function endDate(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                [$hours, $minutes] = explode(':', $attributes['duration']);

                return Carbon::parse($attributes['date'] . ' ' . $attributes['start'])->addHours((int) $hours)->addMinutes((int) $minutes)->format('Y-m-d H:i');
            },
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
