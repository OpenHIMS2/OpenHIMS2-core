<?php

namespace App\Http\Controllers\Clinical;

use App\Http\Controllers\Controller;
use App\Models\BloodPressureReading;
use App\Models\ClinicVisit;
use App\Models\Patient;
use App\Models\Unit;
use App\Models\UnitView;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    // -----------------------------------------------------------------------
    // Helper: ensure the authenticated user owns this UnitView
    // -----------------------------------------------------------------------
    private function authorizeView(UnitView $unitView): void
    {
        if (!Auth::user()->views->contains('id', $unitView->id)) {
            abort(403);
        }
    }

    // -----------------------------------------------------------------------
    // GET /{unitView}/register  — show registration form
    // -----------------------------------------------------------------------
    public function create(UnitView $unitView)
    {
        $this->authorizeView($unitView);
        $unitView->load('unit.institution', 'viewTemplate');

        $pageTitle = $unitView->viewTemplate->name . ' — ' . $unitView->unit->name;
        return view('clinical.patients.register', compact('unitView', 'pageTitle'));
    }

    // -----------------------------------------------------------------------
    // POST /{unitView}/register  — validate, save patient, add to queue
    // -----------------------------------------------------------------------
    public function store(Request $request, UnitView $unitView)
    {
        $this->authorizeView($unitView);

        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'phn'              => 'nullable|string|max:20|unique:patients,phn',
            'dob'              => 'nullable|date|before:today',
            'age'              => 'nullable|integer|min:0|max:150',
            'gender'           => 'required|in:male,female,other',
            'nic'              => 'nullable|string|max:20|unique:patients,nic',
            'mobile'           => 'nullable|string|max:20|unique:patients,mobile',
            'guardian_nic'     => 'nullable|string|max:20',
            'guardian_mobile'  => 'nullable|string|max:20',
            'address'          => 'required|string|max:1000',
            'category'         => 'required|in:opd,new_clinic_visit,recurrent_clinic_visit,urgent',
            'opd_number'       => 'nullable|string|max:20',
            'height'           => 'nullable|numeric|min:1|max:300',
            'weight'           => 'nullable|numeric|min:1|max:500',
            'bp_systolic'      => 'nullable|integer|min:0|max:300',
            'bp_diastolic'     => 'nullable|integer|min:0|max:300',
            'bp_recorded_at'   => 'nullable|date',
            'clinic_number'    => 'nullable|string|max:50',
        ]);

        // Server-side duplicate check
        $existingByNic    = $data['nic']    ? Patient::where('nic', $data['nic'])->first()       : null;
        $existingByMobile = $data['mobile'] ? Patient::where('mobile', $data['mobile'])->first() : null;
        $existing = $existingByNic ?? $existingByMobile;

        if ($existing) {
            $alreadyInQueue = $this->isAlreadyInQueue($existing->id, $unitView->unit_id);
            return back()
                ->withInput()
                ->with('duplicate_patient', $existing)
                ->with('duplicate_already_in_queue', $alreadyInQueue);
        }

        // Null-out empty strings so unique constraint allows multiple nulls
        $data['phn']    = $data['phn']    ?: null;
        $data['nic']    = $data['nic']    ?: null;
        $data['mobile'] = $data['mobile'] ?: null;

        $patient = Patient::create($data);
        $extras  = array_intersect_key($data, array_flip([
            'opd_number', 'height', 'weight', 'bp_systolic', 'bp_diastolic',
        ]));
        $visit = $this->addVisit($patient->id, $unitView->unit_id, $data['category'], $extras);

        if (!empty($data['bp_systolic']) && !empty($data['bp_diastolic'])) {
            BloodPressureReading::create([
                'visit_id'    => $visit->id,
                'systolic'    => $data['bp_systolic'],
                'diastolic'   => $data['bp_diastolic'],
                'recorded_at' => $data['bp_recorded_at'] ?? now(),
                'recorded_by' => Auth::id(),
            ]);
        }

        $ref = $patient->phn ?? $patient->name;
        return redirect()
            ->route('clinical.show', $unitView->id)
            ->with('success', 'Patient ' . $ref . ' registered and added to today\'s queue.');
    }

    // -----------------------------------------------------------------------
    // POST /{unitView}/add-to-queue/{patient}  — add existing patient to today queue
    // -----------------------------------------------------------------------
    public function addToQueue(Request $request, UnitView $unitView, Patient $patient)
    {
        $this->authorizeView($unitView);

        $validated = $request->validate([
            'category'       => 'required|in:opd,new_clinic_visit,recurrent_clinic_visit,urgent',
            'opd_number'     => 'nullable|string|max:20',
            'height'         => 'nullable|numeric|min:1|max:300',
            'weight'         => 'nullable|numeric|min:1|max:500',
            'bp_systolic'    => 'nullable|integer|min:0|max:300',
            'bp_diastolic'   => 'nullable|integer|min:0|max:300',
            'bp_recorded_at' => 'nullable|date',
            'clinic_number'  => 'nullable|string|max:50',
        ]);

        if ($this->isAlreadyInQueue($patient->id, $unitView->unit_id)) {
            if ($request->ajax()) {
                return response()->json(['status' => 'already_in_queue']);
            }
            return redirect()
                ->route('clinical.show', $unitView->id)
                ->with('info', $patient->name . ' is already in today\'s queue.');
        }

        $extras = array_intersect_key($validated, array_flip([
            'opd_number', 'height', 'weight', 'bp_systolic', 'bp_diastolic',
        ]));
        $visit = $this->addVisit($patient->id, $unitView->unit_id, $validated['category'], $extras);

        if (!empty($validated['bp_systolic']) && !empty($validated['bp_diastolic'])) {
            BloodPressureReading::create([
                'visit_id'    => $visit->id,
                'systolic'    => $validated['bp_systolic'],
                'diastolic'   => $validated['bp_diastolic'],
                'recorded_at' => $validated['bp_recorded_at'] ?? now(),
                'recorded_by' => Auth::id(),
            ]);
        }

        if ($request->ajax()) {
            return response()->json(['status' => 'ok']);
        }
        return redirect()
            ->route('clinical.show', $unitView->id)
            ->with('success', $patient->name . ' added to today\'s queue.');
    }

    // -----------------------------------------------------------------------
    // POST /{unitView}/check-duplicate  — AJAX duplicate lookup
    // Accepts optional exclude_id to skip the current patient (used on edit)
    // -----------------------------------------------------------------------
    public function checkDuplicate(Request $request, UnitView $unitView)
    {
        $this->authorizeView($unitView);

        $nic       = $request->input('nic');
        $mobile    = $request->input('mobile');
        $excludeId = (int) $request->input('exclude_id', 0);

        $patient = null;
        if ($nic) {
            $patient = Patient::where('nic', $nic)
                ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
                ->first();
        }
        if (!$patient && $mobile) {
            $patient = Patient::where('mobile', $mobile)
                ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
                ->first();
        }

        if (!$patient) {
            return response()->json(['found' => false]);
        }

        $alreadyInQueue = $this->isAlreadyInQueue($patient->id, $unitView->unit_id);

        return response()->json([
            'found'            => true,
            'already_in_queue' => $alreadyInQueue,
            'patient'          => [
                'id'     => $patient->id,
                'name'   => $patient->name,
                'phn'    => $patient->phn,
                'nic'    => $patient->nic,
                'mobile' => $patient->mobile,
                'gender' => $patient->gender,
                'age'    => $patient->computed_age,
            ],
        ]);
    }

    // -----------------------------------------------------------------------
    // GET /{unitView}/patients/{patient}/edit  — show edit form
    // -----------------------------------------------------------------------
    public function edit(UnitView $unitView, Patient $patient)
    {
        $this->authorizeView($unitView);
        $unitView->load('unit.institution', 'viewTemplate');

        $pageTitle = 'Edit Patient — ' . $unitView->unit->name;
        return view('clinical.patients.edit', compact('unitView', 'patient', 'pageTitle'));
    }

    // -----------------------------------------------------------------------
    // PATCH /{unitView}/patients/{patient}  — save edits
    // -----------------------------------------------------------------------
    public function update(Request $request, UnitView $unitView, Patient $patient)
    {
        $this->authorizeView($unitView);

        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'phn'              => "nullable|string|max:20|unique:patients,phn,{$patient->id}",
            'dob'              => 'nullable|date|before:today',
            'age'              => 'nullable|integer|min:0|max:150',
            'gender'           => 'required|in:male,female,other',
            'nic'              => "nullable|string|max:20|unique:patients,nic,{$patient->id}",
            'mobile'           => "nullable|string|max:20|unique:patients,mobile,{$patient->id}",
            'guardian_nic'     => 'nullable|string|max:20',
            'guardian_mobile'  => 'nullable|string|max:20',
            'address'          => 'required|string|max:1000',
        ]);

        $data['phn']    = $data['phn']    ?: null;
        $data['nic']    = $data['nic']    ?: null;
        $data['mobile'] = $data['mobile'] ?: null;

        $patient->update($data);

        return redirect()
            ->route('clinical.show', $unitView->id)
            ->with('success', 'Patient details updated successfully.');
    }

    // -----------------------------------------------------------------------
    // GET /{unitView}/patients  — AJAX patient history list (partial HTML)
    // -----------------------------------------------------------------------
    public function patientList(Request $request, UnitView $unitView)
    {
        $this->authorizeView($unitView);

        $unitId = $unitView->unit_id;
        $search = $request->input('search');
        $date   = $request->input('date');
        $month  = $request->input('month');
        $year   = $request->input('year');

        // Latest visit per patient in this unit
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
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nic', 'like', "%{$search}%")
                  ->orWhere('phn', 'like', "%{$search}%")
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

        // Determine which patients are already in the current queue session
        $today   = now()->toDateString();
        $session = $this->currentSession($unitId);
        $inQueueToday = ClinicVisit::where('unit_id', $unitId)
            ->where('visit_date', $today)
            ->where('queue_session', $session)
            ->whereIn('status', ['waiting', 'in_progress'])
            ->pluck('patient_id')
            ->toArray();

        // Most recent clinic number per patient in this unit (for pre-filling the modal)
        $patientIds   = $visits->pluck('patient_id')->toArray();
        $clinicNumbers = ClinicVisit::whereIn('patient_id', $patientIds)
            ->where('unit_id', $unitId)
            ->whereIn('category', ['new_clinic_visit', 'recurrent_clinic_visit'])
            ->whereNotNull('clinic_number')
            ->orderByDesc('visit_date')
            ->get()
            ->groupBy('patient_id')
            ->map(fn ($g) => $g->first()->clinic_number);

        // Patients who have already had a new_clinic_visit in this unit (hide that option in queue modal)
        $hasClinicVisit = ClinicVisit::whereIn('patient_id', $patientIds)
            ->where('unit_id', $unitId)
            ->where('category', 'new_clinic_visit')
            ->pluck('patient_id')
            ->unique()
            ->toArray();

        return view('clinical.patients._visit_list', compact('visits', 'unitView', 'inQueueToday', 'clinicNumbers', 'hasClinicVisit'));
    }

    // -----------------------------------------------------------------------
    // GET /{unitView}/today-queue  — AJAX today's queue (partial HTML)
    // -----------------------------------------------------------------------
    public function todayQueue(Request $request, UnitView $unitView)
    {
        $this->authorizeView($unitView);

        $today   = now()->toDateString();
        $session = $this->currentSession($unitView->unit_id);

        $grouped = ClinicVisit::with('patient')
            ->where('unit_id', $unitView->unit_id)
            ->where('visit_date', $today)
            ->where('queue_session', $session)
            ->whereIn('status', ['waiting', 'in_progress'])
            ->orderBy('visit_number')
            ->get()
            ->groupBy('category');

        return view('clinical.patients._queue', compact('grouped', 'unitView'));
    }

    // -----------------------------------------------------------------------
    // GET /{unitView}/gmc-queue  — AJAX GMC 4-tab queue partial
    // -----------------------------------------------------------------------
    public function gmcQueue(UnitView $unitView)
    {
        $this->authorizeView($unitView);

        $today   = now()->toDateString();
        $session = $this->currentSession($unitView->unit_id);

        $grouped = ClinicVisit::with('patient')
            ->where('unit_id', $unitView->unit_id)
            ->where('visit_date', $today)
            ->where('queue_session', $session)
            ->whereIn('status', ['waiting', 'in_progress'])
            ->orderBy('visit_number')
            ->get()
            ->groupBy('category');

        return view('clinical.gmc._queue', compact('grouped', 'unitView'));
    }

    // -----------------------------------------------------------------------
    // POST /{unitView}/queue/{visit}/remove  — cancel a waiting visit
    // -----------------------------------------------------------------------
    public function removeFromQueue(UnitView $unitView, ClinicVisit $visit)
    {
        $this->authorizeView($unitView);
        abort_if($visit->unit_id !== $unitView->unit_id, 403);

        if ($visit->status === 'waiting') {
            $visit->update(['status' => 'cancelled']);
        }

        return response()->json(['status' => 'ok']);
    }

    // -----------------------------------------------------------------------
    // POST /{unitView}/queue/reset  — start a new queue session (numbers from 1)
    // -----------------------------------------------------------------------
    public function resetQueue(UnitView $unitView)
    {
        $this->authorizeView($unitView);

        $today   = now()->toDateString();
        $session = $this->currentSession($unitView->unit_id);

        // Block if any patient is currently in consultation (any session today)
        $hasInProgress = ClinicVisit::where('unit_id', $unitView->unit_id)
            ->where('visit_date', $today)
            ->where('status', 'in_progress')
            ->exists();

        if ($hasInProgress) {
            return response()->json([
                'status'  => 'error',
                'message' => 'A patient is currently in consultation. Please end that visit before starting a new queue.',
            ], 409);
        }

        // Mark ALL of today's waiting visits as visited (all sessions),
        // so no patient is left with an orphaned 'waiting' status.
        ClinicVisit::where('unit_id', $unitView->unit_id)
            ->where('visit_date', $today)
            ->where('status', 'waiting')
            ->update(['status' => 'visited']);

        // Start new session — visit numbers will restart from 1 per category
        Unit::where('id', $unitView->unit_id)->update([
            'current_queue_session' => DB::raw('current_queue_session + 1'),
            'queue_started_at'      => now(),
        ]);

        return response()->json(['status' => 'ok']);
    }

    // -----------------------------------------------------------------------
    // Private helpers
    // -----------------------------------------------------------------------

    private function currentSession(int $unitId): int
    {
        return (int) Unit::where('id', $unitId)->value('current_queue_session') ?: 1;
    }

    private function isAlreadyInQueue(int $patientId, int $unitId): bool
    {
        $session = $this->currentSession($unitId);

        return ClinicVisit::where('patient_id', $patientId)
            ->where('unit_id', $unitId)
            ->where('visit_date', now()->toDateString())
            ->where('queue_session', $session)
            ->whereIn('status', ['waiting', 'in_progress'])
            ->exists();
    }

    private function addVisit(int $patientId, int $unitId, string $category, array $extras = []): ClinicVisit
    {
        $today   = now()->toDateString();
        $session = $this->currentSession($unitId);

        // Build institution code path outside the transaction (avoids nested lock issues)
        $unit       = Unit::with('institution')->find($unitId);
        $codePath   = $unit->institution ? $unit->institution->codePath() : [];
        $unitNumber = $unit->unit_number;

        return DB::transaction(function () use ($patientId, $unitId, $today, $category, $session, $extras, $codePath, $unitNumber) {
            // Lock all today's visits for this unit once — derive both counters from the same set
            $existing = ClinicVisit::where('unit_id', $unitId)
                ->where('visit_date', $today)
                ->lockForUpdate()
                ->get(['id', 'category', 'visit_number', 'queue_session']);

            $maxNum = $existing
                ->where('category', $category)
                ->where('queue_session', $session)
                ->max('visit_number') ?? 0;

            // Auto-increment across all categories for this unit+date (daily sequence)
            $dailySeq = $existing->count() + 1;

            $categoryCode = [
                'opd'                    => '1',
                'new_clinic_visit'       => '2',
                'recurrent_clinic_visit' => '3',
                'urgent'                 => '4',
            ][$category] ?? '0';

            $d         = Carbon::parse($today);
            $segments  = array_merge(
                $codePath,
                [$d->format('d'), $d->format('m'), $d->format('y')]
            );
            if ($unitNumber !== null && $unitNumber !== '') {
                $segments[] = $unitNumber;
            }
            $segments[] = $categoryCode;
            $segments[] = str_pad($dailySeq, 3, '0', STR_PAD_LEFT);
            $visitRef   = implode('/', $segments);

            return ClinicVisit::create([
                'patient_id'    => $patientId,
                'unit_id'       => $unitId,
                'visit_date'    => $today,
                'visit_number'  => $maxNum + 1,
                'queue_session' => $session,
                'category'      => $category,
                'status'        => 'waiting',
                'registered_by' => Auth::id(),
                'opd_number'    => $extras['opd_number']  ?? null,
                'height'        => $extras['height']       ?? null,
                'weight'        => $extras['weight']       ?? null,
                'bp_systolic'   => $extras['bp_systolic']  ?? null,
                'bp_diastolic'  => $extras['bp_diastolic'] ?? null,
                'clinic_number' => $visitRef,
            ]);
        });
    }
}
