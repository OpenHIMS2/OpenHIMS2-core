<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TerminologyCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'is_system', 'sort_order'];
    protected $casts    = ['is_system' => 'boolean'];

    public function terms(): HasMany
    {
        return $this->hasMany(TerminologyTerm::class, 'category', 'slug');
    }
}
