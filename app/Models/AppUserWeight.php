<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppUserWeight extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = "app_user_weights";
    protected $fillable = [
        'app_user_id',
        'date',
        'weight',
    ]; 

    public function users()
    {
        return $this->belongsTo(AppUser::class, 'app_user_id');
    }
}
