<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable
{
    use HasApiTokens,HasRoles,SoftDeletes, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = "users";
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'company',
        'street',
        'street2',
        'city',
        'state',
        'zipcode',
        'is_report_shared',
        'is_non_profit',
        'password',
        'country_code',
        'phone_number',
        'two_factor_code',
        'two_factor_expires_at',
        'two_factor_enable',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getIsUserAttribute() {
        return $this->hasRole('User');
    }

    public function getIsAdminAttribute() {
        return $this->hasRole('Admin');
    }


    public function isAdmin() {
        return $this->roles()->where('name', 'Admin')->exists();
    }
    public function isUser() {
        return $this->roles()->where('name', 'User')->exists();
    }

    // public function getJWTIdentifier()
    // {
    //     return $this->getKey();
    // }
    // public function getJWTCustomClaims()
    // {
    //     return [];
    // }

}
