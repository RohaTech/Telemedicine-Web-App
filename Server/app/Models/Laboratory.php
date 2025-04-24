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
        'location',
        'city',
        'region',
        'status',
        'license',
        'tests'
    ];
    protected $hidden = [
        'password',
    ];

    public function labRequest()
    {
        return $this->hasMany(LabRequest::class);
    }

    public function labResult()
    {
        return $this->hasMany(LabResult::class);
    }
}
