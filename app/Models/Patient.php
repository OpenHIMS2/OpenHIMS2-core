<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'name', 'dob', 'age', 'gender',
        'nic', 'mobile', 'phn',
        'guardian_nic', 'guardian_mobile',
        'address',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    public function getComputedAgeAttribute(): ?int
    {
        if ($this->dob) {
            return Carbon::parse($this->dob)->age;
        }
        return $this->age;
    }

    public function visits()
    {
        return $this->hasMany(ClinicVisit::class);
    }

    public function allergies()
    {
        return $this->hasMany(PatientAllergy::class);
    }
}
