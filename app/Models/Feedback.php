<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feedback extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = "feedbacks";

    protected $fillable = [
        'app_user_id',
        'message',
    ];

    public function users()
    {
        return $this->belongsTo(AppUser::class, 'app_user_id');
    }
}
