<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code'];

    public function viewTemplates()
    {
        return $this->hasMany(ViewTemplate::class);
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }
}
