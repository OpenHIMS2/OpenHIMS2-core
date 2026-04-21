<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitView extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'unit_id', 'view_template_id'];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function viewTemplate()
    {
        return $this->belongsTo(ViewTemplate::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_views');
    }
}
