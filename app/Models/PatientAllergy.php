<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientAllergy extends Model
{
    protected $fillable = ['patient_id', 'allergen'];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
