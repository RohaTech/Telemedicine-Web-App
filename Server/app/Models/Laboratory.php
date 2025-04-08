<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Laboratory extends Model
{
    use HasFactory, HasApiTokens;


    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
    ];
    protected $hidden = [
        'password',
    ];
}
