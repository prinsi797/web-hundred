<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppUserPowerclean extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = "app_user_powercleans";
    protected $fillable = [
        'app_user_id',
        'date',
        'power_clean',
    ]; 

    public function users()
    {
        return $this->belongsTo(AppUser::class, 'app_user_id');
    }
}
