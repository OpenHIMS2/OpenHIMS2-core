<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BloodPressureReading extends Model
{
    protected $fillable = ['visit_id', 'systolic', 'diastolic', 'recorded_at', 'recorded_by'];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    public function visit()
    {
        return $this->belongsTo(ClinicVisit::class, 'visit_id');
    }
}
