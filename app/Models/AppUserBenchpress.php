<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppUserBenchpress extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = "app_user_benchpresses";

    protected $fillable = [
        'app_user_id',
        'date',
        'bench_press',
    ]; 

    public function users()
    {
        return $this->belongsTo(AppUser::class, 'app_user_id');
    }
}
