<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFriend extends Model
{
    use HasFactory;

    protected $table = "user_friends";

    protected $fillable = [
        'app_user_id',
        'user_contact_id',
        'dob',
        'phone_number',
        'profile_phone_url',
        'bench',
        'squat',
        'deadlift',
        'is_invite',
    ];
    
}
