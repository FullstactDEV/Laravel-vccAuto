<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'domain_type',
        'username',
        'password',
        'site_id',
        'site_name',
        'is_internal',
        'is_eu_site',
        'is_chinese_site',
        'center_point_latitude',
        'center_point_longitude',
        'is_active',
    ];
    //
    public function courses() {
        return $this->hasMany(Course::class);
    }
}
