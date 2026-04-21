<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'unit_number', 'institution_id', 'unit_template_id', 'queue_started_at', 'current_queue_session'];

    protected $casts = ['queue_started_at' => 'datetime'];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function unitTemplate()
    {
        return $this->belongsTo(UnitTemplate::class);
    }

    public function unitViews()
    {
        return $this->hasMany(UnitView::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_units');
    }
}
