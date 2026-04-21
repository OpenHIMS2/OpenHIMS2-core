<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TerminologyTerm extends Model
{
    protected $fillable = ['category', 'term'];

    /**
     * All terminology categories — key is DB value, value is display label.
     */
    public static array $categories = [
        'presenting_complaints'      => 'Presenting Complaints',
        'complaint_durations'        => 'Complaint Durations',
        'past_medical_history'       => 'Past Medical History',
        'past_surgical_history'      => 'Past Surgical History',
        'social_history'             => 'Social History',
        'menstrual_history'          => 'Menstrual History',
        'investigations'             => 'Investigations',
        'diabetes_instructions'      => 'Diabetes Instructions',
        'hypertension_instructions'  => 'Hypertension Instructions',
        'dyslipidemia_instructions'  => 'Dyslipidemia Instructions',
        'general_instructions'       => 'General Instructions',
        'differential_diagnosis'     => 'Differential Diagnosis',
        'working_diagnosis'          => 'Working Diagnosis',
        'general_looking'            => 'General Looking',
        'cardiology_findings'        => 'Cardiology Examination Findings',
        'respiratory_findings'       => 'Respiratory Examination Findings',
        'abdominal_findings'         => 'Abdominal Examination Findings',
        'neurological_findings'      => 'Neurological Examination',
        'dermatological_findings'    => 'Dermatological Findings',
    ];
}
