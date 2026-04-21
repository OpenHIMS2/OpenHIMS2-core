<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitNote extends Model
{
    protected $fillable = [
        'visit_id',
        'presenting_complaints', 'complaint_durations',
        'past_medical_history',  'past_surgical_history',
        'social_history',        'menstrual_history',
        'general_looking',       'pulse_rate',
        'cardiology_findings',   'respiratory_findings',
        'abdominal_findings',    'neurological_findings',
        'dermatological_findings',
        'management_instruction',
    ];

    protected $casts = [
        'presenting_complaints'   => 'array',
        'complaint_durations'     => 'array',
        'past_medical_history'    => 'array',
        'past_surgical_history'   => 'array',
        'social_history'          => 'array',
        'menstrual_history'       => 'array',
        'general_looking'         => 'array',
        'cardiology_findings'     => 'array',
        'respiratory_findings'    => 'array',
        'abdominal_findings'      => 'array',
        'neurological_findings'   => 'array',
        'dermatological_findings' => 'array',
        'management_instruction'  => 'array',
    ];

    public function visit()
    {
        return $this->belongsTo(ClinicVisit::class, 'visit_id');
    }
}
