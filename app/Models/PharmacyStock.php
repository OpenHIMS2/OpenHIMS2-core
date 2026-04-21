<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PharmacyStock extends Model
{
    protected $table = 'pharmacy_stock';

    protected $fillable = [
        'unit_view_id', 'drug_name', 'initial_amount', 'remaining',
        'expiry_date', 'is_out_of_stock', 'low_stock_threshold',
        'notes', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'expiry_date'     => 'date',
        'is_out_of_stock' => 'boolean',
    ];

    // -----------------------------------------------------------------------
    // Relationships
    // -----------------------------------------------------------------------

    public function unitView()
    {
        return $this->belongsTo(UnitView::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function dispensings()
    {
        return $this->hasMany(PrescriptionDispensing::class, 'stock_id');
    }

    // -----------------------------------------------------------------------
    // Computed attributes
    // -----------------------------------------------------------------------

    /** Days until expiry (negative = already expired, null = no expiry set) */
    public function getDaysUntilExpiryAttribute(): ?int
    {
        return $this->expiry_date
            ? (int) now()->diffInDays($this->expiry_date, false)
            : null;
    }

    /** Status key used by the UI: ok | low | depleted | out_of_stock | expired */
    public function getStockStatusAttribute(): string
    {
        if ($this->expiry_date && $this->expiry_date->isPast()) {
            return 'expired';
        }
        if ($this->is_out_of_stock) {
            return 'out_of_stock';
        }
        if ($this->remaining <= 0) {
            return 'depleted';
        }
        if ($this->remaining <= $this->low_stock_threshold) {
            return 'low';
        }
        return 'ok';
    }

    // -----------------------------------------------------------------------
    // Scopes
    // -----------------------------------------------------------------------

    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->whereNotNull('expiry_date')
                     ->where('expiry_date', '>=', now()->toDateString())
                     ->where('expiry_date', '<=', now()->addDays($days)->toDateString());
    }

    public function scopeNearOutOfStock($query)
    {
        return $query->where('is_out_of_stock', false)
                     ->where('remaining', '>', 0)
                     ->whereColumn('remaining', '<=', 'low_stock_threshold');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where(function ($q) {
            $q->where('is_out_of_stock', true)->orWhere('remaining', '<=', 0);
        });
    }
}
