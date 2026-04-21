<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicVisit extends Model
{
    protected $fillable = [
        'patient_id', 'unit_id', 'institution_id', 'visit_date',
        'visit_number', 'queue_session', 'category', 'status', 'registered_by',
        'opd_number', 'height', 'weight', 'bp_systolic', 'bp_diastolic', 'clinic_number',
    ];

    protected $casts = [
        'visit_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (ClinicVisit $visit) {
            if ($visit->institution_id === null && $visit->unit_id) {
                $visit->institution_id = Unit::where('id', $visit->unit_id)->value('institution_id');
            }
        });
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function note()
    {
        return $this->hasOne(VisitNote::class, 'visit_id');
    }

    public function bpReadings()
    {
        return $this->hasMany(BloodPressureReading::class, 'visit_id')->orderBy('recorded_at');
    }

    public function investigations()
    {
        return $this->hasMany(Investigation::class, 'visit_id')->orderBy('recorded_at');
    }

    public function drugs()
    {
        return $this->hasMany(VisitDrug::class, 'visit_id')->orderBy('created_at');
    }

    public function drugChanges()
    {
        return $this->hasMany(VisitDrugChange::class, 'visit_id')->orderByDesc('created_at');
    }

    public function dispensings()
    {
        return $this->hasMany(PrescriptionDispensing::class, 'visit_id');
    }
}
