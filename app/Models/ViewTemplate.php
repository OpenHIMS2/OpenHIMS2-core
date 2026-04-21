<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'blade_path', 'unit_template_id'];

    public function unitTemplate()
    {
        return $this->belongsTo(UnitTemplate::class);
    }

    public function unitViews()
    {
        return $this->hasMany(UnitView::class);
    }
}
