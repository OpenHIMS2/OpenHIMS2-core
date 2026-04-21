<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrescriptionDispensing extends Model
{
    protected $table = 'prescription_dispensings';

    protected $fillable = [
        'visit_id', 'visit_drug_id', 'stock_id', 'status',
        'quantity_dispensed', 'dispensed_by', 'dispensed_at',
    ];

    protected $casts = [
        'dispensed_at' => 'datetime',
    ];

    public function visit()
    {
        return $this->belongsTo(ClinicVisit::class, 'visit_id');
    }

    public function visitDrug()
    {
        return $this->belongsTo(VisitDrug::class, 'visit_drug_id');
    }

    public function stock()
    {
        return $this->belongsTo(PharmacyStock::class, 'stock_id');
    }

    public function dispensedBy()
    {
        return $this->belongsTo(User::class, 'dispensed_by');
    }
}
