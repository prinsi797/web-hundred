<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppUserSchool extends Model
{
    use HasFactory;

    protected $table = "app_user_schools";

    protected $fillable = [
        'app_user_id',
        'school_id',
    ];

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }

}
