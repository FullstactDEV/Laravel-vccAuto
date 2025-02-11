<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PinRotationMarker extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'course_name',
        'set_type_id',
        'key',
        'marker',
    ];
}
