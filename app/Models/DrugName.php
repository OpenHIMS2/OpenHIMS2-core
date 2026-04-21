<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrugName extends Model
{
    protected $fillable = ['name'];

    public function default()
    {
        return $this->hasOne(DrugNameDefault::class, 'drug_name_id');
    }
}
