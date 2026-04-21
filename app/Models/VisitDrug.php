<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitDrug extends Model
{
    protected $table = 'visit_drugs';

    protected $fillable = ['visit_id', 'section', 'type', 'name', 'dose', 'unit', 'frequency', 'duration', 'created_by', 'updated_by'];

    /** Short abbreviation used in change-log display. */
    public static array $typeAbbr = [
        'Oral'        => 'O.',
        'S/C'         => 'S/C',
        'IM'          => 'IM',
        'IV'          => 'IV',
        'S/L'         => 'S/L',
        'Syrup'       => 'Syr.',
        'MDI'         => 'MDI',
        'DPI'         => 'DPI',
        'Suppository' => 'Supp.',
        'LA'          => 'LA',
    ];

    /** Short abbreviation used in change-log display. */
    public static array $freqAbbr = [
        'mane'  => 'm',
        'nocte' => 'n',
        'bd'    => 'bd',
        'tds'   => 'tds',
        'daily' => 'daily',
        'EOD'   => 'EOD',
        'SOS'   => 'SOS',
    ];

    /** Returns a compact drug string, e.g. "O.Amitriptyline 12.5mg n" */
    public static function formatDrug(string $type, string $name, string $dose, string $unit, string $frequency): string
    {
        $abbr = static::$typeAbbr[$type] ?? $type;
        $freq = static::$freqAbbr[$frequency] ?? $frequency;
        return "{$abbr}{$name} {$dose}{$unit} {$freq}";
    }

    public function visit()
    {
        return $this->belongsTo(ClinicVisit::class, 'visit_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function dispensings()
    {
        return $this->hasMany(PrescriptionDispensing::class, 'visit_drug_id');
    }
}
