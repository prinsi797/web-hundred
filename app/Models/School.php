<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = "schools";

    protected $fillable = [
        'name',
        'image_url',
        'short_name',
        'website',
        'street',
        'street2',
        'zipcode',
        'state',
        'city',
    ];

}
