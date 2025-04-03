<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'consultation_id',
        'doctor_id',
        'patient_id',
        'laboratory_id',
        'test_type',
        'status',
    ];

    /**
     * A lab request belongs to a consultation.
     */
    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    /**
     * A lab request belongs to a doctor.
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * A lab request belongs to a patient.
     */
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * A lab request belongs to a laboratory.
     */
    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class);
    }
}
