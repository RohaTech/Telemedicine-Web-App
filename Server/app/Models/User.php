<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable,  HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'birth_date',
        'gender',
        'region',
        'city',
        'profile_picture',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    /**
     * A user can be a patient and have multiple consultations.
     */
    public function consultations()
    {
        return $this->hasMany(Consultation::class, 'patient_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    /**
     * A user can be a doctor and have multiple consultations.
     */
    public function doctorConsultations()
    {
        return $this->hasMany(Consultation::class, 'doctor_id');
    }

    /**
     * A user can have multiple payments.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'patient_id');
    }

    /**
     * A user can have multiple prescriptions.
     */
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'patient_id');
    }

    /**
     * A doctor can issue multiple prescriptions.
     */
    public function doctorPrescriptions()
    {
        return $this->hasMany(Prescription::class, 'doctor_id');
    }

    /**
     * A user can have multiple notifications.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * A user can have multiple appointments.
     */


    /**
     * A doctor can have multiple appointments.
     */
    public function doctorAppointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    /**
     * A user can have multiple lab requests as a patient.
     */
    public function labRequests()
    {
        return $this->hasManyThrough(LabRequest::class, Consultation::class, 'patient_id', 'consultation_id', 'id', 'id');
    }
}
