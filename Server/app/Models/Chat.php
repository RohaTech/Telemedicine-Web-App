<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = ['consultation_id', 'sender', 'text', 'timestamp'];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }
}