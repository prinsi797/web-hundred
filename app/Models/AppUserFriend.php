<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppUserFriend extends Model {
    use HasFactory;
    protected $table = "app_user_friends";

    protected $fillable = [
        'app_user_id',
        'app_friend_id',
        'is_added'
    ];

    // Define the relationship with the AppUser model
    public function user() {
        return $this->belongsTo(AppUser::class, 'app_user_id');
    }

    // Define the relationship with the AppUser model for the friend
    public function friend() {
        return $this->belongsTo(AppUser::class, 'app_friend_id');
    }
}
