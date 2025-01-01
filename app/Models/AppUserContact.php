<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppUserContact extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = "app_user_contacts";

    protected $fillable = [
        'app_user_id',
        'country_code',
        'contact_firstname',
        'contact_lastname',
        'contact_phone_number',
    ];

       
    public function appUser()
    {
        return $this->belongsTo(AppUser::class, 'app_user_id');
    }

    public function getUserIdAttribute() {
        $user =  AppUser::where('phone_number', $this->contact_phone_number)->first();
        if ($user) {
            return $user->id;
        }
        return null;
    }
}
