<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_date',
        'time',
        'status',
    ];

    protected $casts = [
        'status' => 'string', // Cast status as string
        'appointment_date' => 'date', // Cast as date
        'time' => 'string', // Time as string (HH:mm:ss)
    ];

    /**
     * An appointment belongs to a patient.
     */
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }




    /**
     * An appointment belongs to a doctor.
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
