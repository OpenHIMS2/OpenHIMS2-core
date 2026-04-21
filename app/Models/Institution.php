<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'parent_id', 'code', 'email', 'phone', 'address', 'logo'];

    public function logoUrl(): ?string
    {
        if (!$this->logo) return null;
        return asset('institution_logos/' . $this->logo);
    }

    public function parent()
    {
        return $this->belongsTo(Institution::class, 'parent_id');
    }

    /**
     * Returns the full code path from the root institution down to this one.
     * e.g. ['CP', 'AK', 'GM']
     */
    public function codePath(): array
    {
        $path    = [];
        $current = $this;
        while ($current) {
            array_unshift($path, strtoupper($current->code ?? '??'));
            $current = $current->parent;   // lazy-loads one level at a time
        }
        return $path;
    }

    public function children()
    {
        return $this->hasMany(Institution::class, 'parent_id');
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren')->orderBy('name');
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
