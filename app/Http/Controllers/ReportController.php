<?php

namespace App\Http\Controllers;

use App\Models\ClinicVisit;
use App\Models\Institution;
use App\Models\Patient;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        $institution = Auth::user()->institution;
        $units = $institution
            ? Unit::where('institution_id', $institution->id)->orderBy('name')->get()
            : Unit::orderBy('name')->get();

        return view('reports.index', [
            'pageTitle'   => 'Reports',
            'institution' => $institution,
            'units'       => $units,
        ]);
    }

    // ── AJAX: search patients ───────────────────────────────────────────────
    public function patientSearch(Request $request)
    {
        $q = trim($request->input('q', ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $patients = Patient::where(function ($query) use ($q) {
            $query->where('name',   'like', "%{$q}%")
                  ->orWhere('phn',    'like', "%{$q}%")
                  ->orWhere('nic',    'like', "%{$q}%")
                  ->orWhere('mobile', 'like', "%{$q}%");
        })
        ->limit(10)
        ->get();

        return response()->json($patients->map(fn($p) => [
            'id'     => $p->id,
            'name'   => $p->name,
            'phn'    => $p->phn   ?? '—',
            'nic'    => $p->nic   ?? '—',
            'age'    => $p->computed_age ?? $p->age ?? '—',
            'gender' => ucfirst($p->gender ?? ''),
        ]));
    }

    // ── AJAX: get patient data for letter ───────────────────────────────────
    public function letterData(Patient $patient)
    {
        $patient->load(['visits.note']);

        // Collect unique past medical history items with earliest year
        $conditions = [];
        foreach ($patient->visits as $visit) {
            $year = date('Y', strtotime($visit->visit_date));
            if ($visit->note && !empty($visit->note->past_medical_history)) {
                foreach ($visit->note->past_medical_history as $condition) {
                    if ($condition && (!isset($conditions[$condition]) || $conditions[$condition] > (int)$year)) {
                        $conditions[$condition] = (int)$year;
                    }
                }
            }
        }
        asort($conditions);

        $conditionList = [];
        foreach ($conditions as $condition => $year) {
            $conditionList[] = ['condition' => $condition, 'year' => $year];
        }

        return response()->json([
            'id'         => $patient->id,
            'name'       => $patient->name,
            'age'        => $patient->computed_age ?? $patient->age ?? '',
            'gender'     => ucfirst($patient->gender ?? ''),
            'address'    => $patient->address  ?? '',
            'mobile'     => $patient->mobile   ?? '',
            'phn'        => $patient->phn      ?? '',
            'nic'        => $patient->nic      ?? '',
            'conditions' => $conditionList,
        ]);
    }

    // ── Print: Clinic Confirmation Letter ───────────────────────────────────
    public function clinicConfirmationLetter(Request $request)
    {
        $institution = Auth::user()->institution;

        // Collect conditions from POST (dynamic list)
        $conditions = [];
        $condNames  = $request->input('condition_name', []);
        $condYears  = $request->input('condition_year', []);
        foreach ($condNames as $i => $name) {
            if (trim($name)) {
                $conditions[] = [
                    'condition' => trim($name),
                    'year'      => $condYears[$i] ?? '',
                ];
            }
        }

        $patientData = [
            'name'    => $request->input('patient_name', ''),
            'age'     => $request->input('patient_age', ''),
            'gender'  => $request->input('patient_gender', ''),
            'address' => $request->input('patient_address', ''),
            'mobile'  => $request->input('patient_mobile', ''),
            'phn'     => $request->input('patient_phn', ''),
            'nic'     => $request->input('patient_nic', ''),
        ];

        return view('reports.prints.clinic-confirmation-letter', [
            'institution' => $institution,
            'patientData' => $patientData,
            'conditions'  => $conditions,
            'printDate'   => now()->format('d F Y'),
        ]);
    }

    // ── Print: Monthly Clinic Report ────────────────────────────────────────
    public function monthlyClinic(Request $request)
    {
        $month  = (int) $request->input('month', now()->month);
        $year   = (int) $request->input('year',  now()->year);
        $unitId = $request->input('unit_id');

        $institution = Auth::user()->institution;
        $unit = $unitId ? Unit::with('institution')->find($unitId) : null;

        if (!$unit && $institution) {
            $unit = Unit::where('institution_id', $institution->id)->first();
        }

        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate   = date('Y-m-t', strtotime($startDate));
        $monthName = date('F', strtotime($startDate));
        $daysInMonth = (int) date('t', strtotime($startDate));

        $visits = $unit
            ? ClinicVisit::with('patient')
                ->where('unit_id', $unit->id)
                ->whereBetween('visit_date', [$startDate, $endDate])
                ->whereNotIn('status', ['cancelled'])
                ->get()
            : collect();

        // Daily breakdown
        $dailyData = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dateKey    = sprintf('%04d-%02d-%02d', $year, $month, $d);
            $dayVisits  = $visits->where('visit_date', $dateKey);
            $dailyData[] = [
                'day'    => $d,
                'date'   => $dateKey,
                'opd'    => $dayVisits->where('category', 'opd')->count(),
                'clinic' => $dayVisits->where('category', 'clinic')->count(),
                'other'  => $dayVisits->whereNotIn('category', ['opd', 'clinic'])->count(),
                'total'  => $dayVisits->count(),
            ];
        }

        // Category totals
        $totalVisits  = $visits->count();
        $totalOpd     = $visits->where('category', 'opd')->count();
        $totalClinic  = $visits->where('category', 'clinic')->count();
        $totalOther   = $visits->whereNotIn('category', ['opd', 'clinic'])->count();

        // Unique patients
        $uniquePatients = $visits->pluck('patient_id')->unique()->count();

        // Peak day
        $peakDay = collect($dailyData)->sortByDesc('total')->first();

        return view('reports.prints.monthly-clinic-report', [
            'institution'    => $institution ?? $unit?->institution,
            'unit'           => $unit,
            'month'          => $month,
            'year'           => $year,
            'monthName'      => $monthName,
            'startDate'      => $startDate,
            'endDate'        => $endDate,
            'daysInMonth'    => $daysInMonth,
            'dailyData'      => $dailyData,
            'totalVisits'    => $totalVisits,
            'totalOpd'       => $totalOpd,
            'totalClinic'    => $totalClinic,
            'totalOther'     => $totalOther,
            'uniquePatients' => $uniquePatients,
            'peakDay'        => $peakDay,
            'printDate'      => now()->format('d F Y'),
        ]);
    }
}
