<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PharmacyRestockLog extends Model
{
    protected $fillable = [
        'unit_view_id', 'stock_id', 'drug_name', 'action',
        'amount', 'expiry_date', 'notes', 'performed_by',
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function stock()
    {
        return $this->belongsTo(PharmacyStock::class, 'stock_id');
    }
}
