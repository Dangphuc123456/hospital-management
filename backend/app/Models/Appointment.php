<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;
    protected $fillable = [
        'patient_id', 'doctor_id', 'schedule_id',
        'appointment_date', 'appointment_time', 'type',
        'is_paid', 'status', 'notes'
    ];

    protected $casts = [
        'is_paid' => 'boolean',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function prescription()
    {
        return $this->hasOne(Prescription::class);
    }

    public function medicalRecord()
    {
        return $this->hasOne(MedicalRecord::class);
    }
}
