<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Laboratory extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $guard = 'laboratory';

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
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'location' => 'array',
        'tests' => 'array',
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
