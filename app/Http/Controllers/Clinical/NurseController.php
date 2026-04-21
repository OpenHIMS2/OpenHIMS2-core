<?php

namespace App\Http\Controllers\Clinical;

use App\Http\Controllers\Controller;
use App\Models\ClinicVisit;
use App\Models\Patient;
use App\Models\Unit;
use App\Models\UnitView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NurseController extends Controller
{
    private function authorizeView(UnitView $unitView): void
    {
        if (!Auth::user()->views->contains('id', $unitView->id)) {
            abort(403);
        }
    }

    private function currentSession(int $unitId): int
    {
        return (int) Unit::where('id', $unitId)->value('current_queue_session') ?: 1;
    }

    // -----------------------------------------------------------------------
    // GET /{unitView}/nurse/patients  — AJAX patient list (nurse partial)
    // Identical query to PatientController::patientList; returns nurse partial
    // so patient names render as links to nurse history.
    // -----------------------------------------------------------------------
    public function patientList(Request $request, UnitView $unitView)
    {
        $this->authorizeView($unitView);

        $unitId = $unitView->unit_id;
        $search = $request->input('search');
        $date   = $request->input('date');
        $month  = $request->input('month');
        $year   = $request->input('year');

        $subQuery = ClinicVisit::select('patient_id', DB::raw('MAX(id) as last_visit_id'))
            ->where('unit_id', $unitId)
            ->groupBy('patient_id');

        $query = ClinicVisit::query()
            ->joinSub($subQuery, 'lv', 'clinic_visits.id', '=', 'lv.last_visit_id')
            ->with('patient')
            ->where('clinic_visits.unit_id', $unitId)
            ->orderByDesc('clinic_visits.visit_date')
            ->orderByDesc('clinic_visits.visit_number');

        if ($search) {
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('name',   'like', "%{$search}%")
                  ->orWhere('nic',  'like', "%{$search}%")
                  ->orWhere('phn',  'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        if ($date) {
            $query->whereDate('clinic_visits.visit_date', $date);
        } elseif ($month && $year) {
            $query->whereMonth('clinic_visits.visit_date', $month)
                  ->whereYear('clinic_visits.visit_date', $year);
        } elseif ($year) {
            $query->whereYear('clinic_visits.visit_date', $year);
        } elseif ($month) {
            $query->whereMonth('clinic_visits.visit_date', $month);
        }

        $visits = $query->paginate(20);

        $today   = now()->toDateString();
        $session = $this->currentSession($unitId);
        $inQueueToday = ClinicVisit::where('unit_id', $unitId)
            ->where('visit_date', $today)
            ->where('queue_session', $session)
            ->whereIn('status', ['waiting', 'in_progress'])
            ->pluck('patient_id')
            ->toArray();

        $patientIds = $visits->pluck('patient_id')->toArray();
        $hasClinicVisit = ClinicVisit::whereIn('patient_id', $patientIds)
            ->where('unit_id', $unitId)
            ->where('category', 'new_clinic_visit')
            ->pluck('patient_id')
            ->unique()
            ->toArray();

        return view('clinical.nurse._visit_list', compact('visits', 'unitView', 'inQueueToday', 'hasClinicVisit'));
    }

    // -----------------------------------------------------------------------
    // GET /{unitView}/nurse/patient/{patient}  — read-only patient history
    // Today's visit is shown as a status badge only (no Start/Continue button).
    // -----------------------------------------------------------------------
    public function patientHistory(Request $request, UnitView $unitView, Patient $patient)
    {
        $this->authorizeView($unitView);
        $unitView->load('unit.institution', 'viewTemplate');
        $patient->load('allergies');

        $today = now()->toDateString();

        $visits = ClinicVisit::where('patient_id', $patient->id)
            ->where('unit_id', $unitView->unit_id)
            ->orderByDesc('visit_date')
            ->orderByDesc('visit_number')
            ->get();

        $todayVisit = $visits->first(
            fn($v) => $v->visit_date->toDateString() === $today
                   && in_array($v->status, ['waiting', 'in_progress'])
        );

        $pastVisits = $visits->filter(
            fn($v) => $v->visit_date->toDateString() !== $today
                   || $v->status === 'visited'
        )->values();

        if ($pastVisits->isNotEmpty()) {
            $pastVisits->load('note', 'bpReadings', 'investigations', 'drugs');
        }

        $selectedId    = (int) $request->query('visit', 0);
        $selectedVisit = $pastVisits->isNotEmpty()
            ? ($selectedId ? $pastVisits->firstWhere('id', $selectedId) : $pastVisits->first())
            : null;

        $pageTitle = $patient->name . ' — ' . $unitView->unit->name;
        return view('clinical.nurse.patient_history',
            compact('unitView', 'patient', 'todayVisit', 'pastVisits', 'pageTitle', 'selectedVisit'));
    }

    // -----------------------------------------------------------------------
    // GET /{unitView}/nurse/visit/{visit}  — past visit summary (read-only)
    // -----------------------------------------------------------------------
    public function visitSummary(UnitView $unitView, ClinicVisit $visit)
    {
        $this->authorizeView($unitView);
        abort_if($visit->unit_id !== $unitView->unit_id, 403);

        $visit->load('patient');
        $unitView->load('unit.institution', 'viewTemplate');

        $pageTitle = 'Visit Summary — ' . $visit->patient->name;
        return view('clinical.nurse.visit_summary', compact('unitView', 'visit', 'pageTitle'));
    }
}
