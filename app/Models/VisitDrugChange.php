<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitDrugChange extends Model
{
    protected $table = 'visit_drug_changes';

    protected $fillable = ['visit_id', 'drug_id', 'user_id', 'action', 'old_values', 'new_values'];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function visit()
    {
        return $this->belongsTo(ClinicVisit::class, 'visit_id');
    }

    /** Human-readable sentence describing this change. */
    public function toSentence(): string
    {
        $doctor = optional($this->user)->name ?? 'Unknown';
        $at     = $this->created_at->format('d/m/Y');

        switch ($this->action) {
            case 'added':
                $drug = VisitDrug::formatDrug(
                    $this->new_values['type'],
                    $this->new_values['name'],
                    $this->new_values['dose'],
                    $this->new_values['unit'],
                    $this->new_values['frequency']
                );
                return "Dr. {$doctor} added {$drug} on {$at}";

            case 'edited':
                $old = VisitDrug::formatDrug(
                    $this->old_values['type'],
                    $this->old_values['name'],
                    $this->old_values['dose'],
                    $this->old_values['unit'],
                    $this->old_values['frequency']
                );
                $new = VisitDrug::formatDrug(
                    $this->new_values['type'],
                    $this->new_values['name'],
                    $this->new_values['dose'],
                    $this->new_values['unit'],
                    $this->new_values['frequency']
                );
                return "Dr. {$doctor} edited {$old} → {$new} on {$at}";

            case 'deleted':
                $drug = VisitDrug::formatDrug(
                    $this->old_values['type'],
                    $this->old_values['name'],
                    $this->old_values['dose'],
                    $this->old_values['unit'],
                    $this->old_values['frequency']
                );
                return "Dr. {$doctor} discontinued {$drug} on {$at}";
        }

        return '';
    }
}
