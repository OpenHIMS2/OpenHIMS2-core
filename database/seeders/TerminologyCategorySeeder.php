<?php

namespace Database\Seeders;

use App\Models\TerminologyCategory;
use Illuminate\Database\Seeder;

class TerminologyCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['slug' => 'presenting_complaints',     'name' => 'Presenting Complaints'],
            ['slug' => 'complaint_durations',       'name' => 'Complaint Durations'],
            ['slug' => 'past_medical_history',      'name' => 'Past Medical History'],
            ['slug' => 'past_surgical_history',     'name' => 'Past Surgical History'],
            ['slug' => 'social_history',            'name' => 'Social History'],
            ['slug' => 'menstrual_history',         'name' => 'Menstrual History'],
            ['slug' => 'investigations',            'name' => 'Investigations'],
            ['slug' => 'diabetes_instructions',     'name' => 'Diabetes Instructions'],
            ['slug' => 'hypertension_instructions', 'name' => 'Hypertension Instructions'],
            ['slug' => 'dyslipidemia_instructions', 'name' => 'Dyslipidemia Instructions'],
            ['slug' => 'general_instructions',      'name' => 'General Instructions'],
            ['slug' => 'differential_diagnosis',    'name' => 'Differential Diagnosis'],
            ['slug' => 'working_diagnosis',         'name' => 'Working Diagnosis'],
            ['slug' => 'general_looking',           'name' => 'General Looking'],
            ['slug' => 'cardiology_findings',       'name' => 'Cardiology Examination Findings'],
            ['slug' => 'respiratory_findings',      'name' => 'Respiratory Examination Findings'],
            ['slug' => 'abdominal_findings',        'name' => 'Abdominal Examination Findings'],
            ['slug' => 'neurological_findings',     'name' => 'Neurological Examination'],
            ['slug' => 'dermatological_findings',   'name' => 'Dermatological Findings'],
        ];

        foreach ($categories as $i => $cat) {
            TerminologyCategory::firstOrCreate(
                ['slug' => $cat['slug']],
                array_merge($cat, ['is_system' => true, 'sort_order' => $i])
            );
        }
    }
}
