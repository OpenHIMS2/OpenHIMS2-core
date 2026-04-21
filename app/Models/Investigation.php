<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Investigation extends Model
{
    protected $fillable = ['visit_id', 'name', 'value', 'recorded_at', 'recorded_by'];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    public function visit()
    {
        return $this->belongsTo(ClinicVisit::class, 'visit_id');
    }
}
