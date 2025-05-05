<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    public function name(): Attribute
    {
        return Attribute::make(
            set: function ($value) {
                return ucwords(strtolower($value));
            },
        );
    }
}
