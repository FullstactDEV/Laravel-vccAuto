<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VccCredential extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'domain_type',
        'username',
        'password',
        'site_id',
        'site_name',
        'center_point_latitude',
        'center_point_longitude',
        'is_active',
    ];
}
