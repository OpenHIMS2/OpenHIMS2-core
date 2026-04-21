<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrugNameDefault extends Model
{
    protected $fillable = ['drug_name_id', 'type', 'dose', 'unit', 'frequency', 'duration'];

    public function drugName()
    {
        return $this->belongsTo(DrugName::class, 'drug_name_id');
    }
}
