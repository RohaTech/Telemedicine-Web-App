<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'prescription_id',
        'appointment_id',
        'consultation_date',
        'notes',
    ];

    /**
     * A consultation belongs to a patient.
     */
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * A consultation belongs to a doctor.
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * A consultation may have a prescription.
     */
    public function prescription()
    {
        return $this->hasOne(Prescription::class);
    }
    /**
     * A consultation belongs to an appointment.
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
