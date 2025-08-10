<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'medical_license_number',
        'specialty',
        'qualification',
        'experience_years',
        'university_attended',
        'license_issue_date',
        'license_expiry_date',
        'status',
        'payment',
        'location',
        'certificate_path',
        'languages',
        'overview',
        'available',
    ];

    /**
     * A doctor belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A doctor has many consultations.
     */
    public function consultations()
    {
        return $this->hasMany(Consultation::class, 'doctor_id');
    }

    /**
     * A doctor issues many prescriptions.
     */
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    /**
     * A doctor has many payments.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'doctor_id');
    }

    /**
     * A doctor has many appointments.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }
}
