<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'domain_type',
        'club_id',
        'site_id',
        'site_name',
        'course_id',
        'course_name',
        'course_number',
        'first_hole',
        'hole_count',
        'site_settings',
    ];
    //
    public function club() {
        return $this->belongsTo(Club::class, 'club_id');
    }
}
