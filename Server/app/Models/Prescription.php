<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'patient_id',
        'consultation_id',
        'medicine_name',
        'directions',
    ];

    /**
     * A prescription belongs to a doctor.
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * A prescription belongs to a patient.
     */
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * A prescription belongs to a consultation.
     */
    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }
}
