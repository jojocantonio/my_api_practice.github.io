<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Carbon\Carbon;


class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'eventName',
        'frequency',
        'duration',
        'startDateTime',
        'endDateTime',
        'invitees'
    ];


    // public function setStartDateTimeAttribute($value)
    // {
    //     $this->attributes['startDateTime'] = (new Carbon($value))->format('Y-m-d H:i');
    // }

    public function setInviteesAttribute($value)
    {
        $this->attributes['invitees'] = json_encode($value);
    }
}
