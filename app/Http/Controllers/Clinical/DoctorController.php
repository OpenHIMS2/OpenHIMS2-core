<?php

namespace App\Http\Controllers\Clinical;

use App\Http\Controllers\Controller;
use App\Models\BloodPressureReading;
use App\Models\Investigation;
use App\Models\PharmacyStock;
use App\Models\VisitDrug;
use App\Models\VisitDrugChange;
use App\Models\ClinicVisit;
use App\Models\Patient;
use App\Models\PatientAllergy;
use App\Models\Unit;
use App\Models\UnitView;
use App\Models\VisitNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{
    private function authorizeView(UnitView $unitView): void
    {
        if (!Auth::user()->views->contains('id', $unitView->id)) {
            abort(403);
        }
    }

    // -----------------------------------------------------------------------
    // GET /{unitView}/doctor/patient-search?q=  — AJAX patient search (JSON)
    // -----------------------------------------------------------------------
    public function patientSearch(Request $request, UnitView $unitView)
    {
        $this->authorizeView($unitView);

        $q = trim($request->input('q', ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $patients = Patient::whereHas('visits', fn($vq) => $vq->where('unit_id', $unitView->unit_id))
            ->where(function ($query) use ($q) {
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
            'phn'    => $p->phn ?? '—',
            'age'    => $p->computed_age ?? '—',
            'gender' => ucfirst($p->gender),
            'url'    => route('clinical.doctor.patient-history', [$unitView->id, $p->id]),
        ]));
    }

    // -----------------------------------------------------------------------
    // GET /{unitView}/doctor-queue  — AJAX queue partial
    // -----------------------------------------------------------------------
    public function queuePartial(UnitView $unitView)
    {
        $this->authorizeView($unitView);

        $today   = now()->toDateString();
        $session = (int) Unit::where('id', $unitView->unit_id)->value('current_queue_session') ?: 1;

        $active = ClinicVisit::with('patient')
            ->where('unit_id', $unitView->unit_id)
            ->where('visit_date', $today)
            ->where('queue_session', $session)
            ->whereIn('status', ['waiting', 'in_progress'])
            ->orderBy('visit_number')
            ->get();

        $inProgress = $active->where('status', 'in_progress')->values();
        $waiting    = $active->where('status', 'waiting')->groupBy('category');

        $visitedCount = ClinicVisit::where('unit_id', $unitView->unit_id)
            ->where('visit_date', $today)
            ->where('status', 'visited')
            ->count();

        return view('clinical.doctor._queue',
            compact('inProgress', 'waiting', 'visitedCount', 'unitView'));
    }

    // -----------------------------------------------------------------------
    // GET /{unitView}/doctor/patient/{patient}  — patient visit history
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

        // Only show the "Today's Visit" action card while the visit is still active.
        // Once it is 'visited' it belongs in the past visits list.
        $todayVisit = $visits->first(
            fn($v) => $v->visit_date->toDateString() === $today
                   && in_array($v->status, ['waiting', 'in_progress'])
        );

        $pastVisits = $visits->filter(
            fn($v) => $v->visit_date->toDateString() !== $today
                   || $v->status === 'visited'
        )->values();

        // Load all relations for every past visit (needed for summary aggregations).
        if ($pastVisits->isNotEmpty()) {
            $pastVisits->load('note', 'bpReadings', 'investigations', 'drugs');
        }

        // Determine which visit to show in the Visit History detail panel.
        $selectedVisit = null;
        if ($pastVisits->isNotEmpty()) {
            $selectedId    = (int) $request->query('visit', 0);
            $selectedVisit = $selectedId
                ? $pastVisits->firstWhere('id', $selectedId)
                : $pastVisits->first();
        }

        $pageTitle = $patient->name . ' — ' . $unitView->unit->name;
        return view('clinical.doctor.patient_history',
            compact('unitView', 'patient', 'todayVisit', 'pastVisits', 'pageTitle', 'selectedVisit'));
    }

    // -----------------------------------------------------------------------
    // POST /{unitView}/doctor/visit/{visit}/start  — waiting → in_progress
    // -----------------------------------------------------------------------
    public function startVisit(UnitView $unitView, ClinicVisit $visit)
    {
        $this->authorizeView($unitView);
        abort_if($visit->unit_id !== $unitView->unit_id, 403);

        if ($visit->status === 'waiting') {
            $visit->update(['status' => 'in_progress']);

            // Pre-fill from the last completed visit for recurrent, OPD and urgent visits
            if (in_array($visit->category, ['recurrent_clinic_visit', 'opd', 'urgent']) && !$visit->note()->exists()) {
                $prev = ClinicVisit::with(['note', 'drugs'])
                    ->where('patient_id', $visit->patient_id)
                    ->where('unit_id',    $visit->unit_id)
                    ->where('status',     'visited')
                    ->where('id',         '!=', $visit->id)
                    ->orderByDesc('visit_date')
                    ->orderByDesc('visit_number')
                    ->first();

                if ($prev) {
                    if ($prev->note) {
                        // management_instruction is intentionally excluded —
                        // it is visit-specific and must always start empty.
                        VisitNote::create(array_merge(
                            ['visit_id' => $visit->id],
                            $prev->note->only([
                                'presenting_complaints', 'complaint_durations',
                                'past_medical_history',  'past_surgical_history',
                                'social_history',        'menstrual_history',
                                'general_looking',       'pulse_rate',
                                'cardiology_findings',   'respiratory_findings',
                                'abdominal_findings',    'neurological_findings',
                                'dermatological_findings',
                            ])
                        ));
                    }

                    foreach ($prev->drugs->where('section', 'clinic') as $drug) {
                        $visit->drugs()->create([
                            'section'    => 'clinic',
                            'type'       => $drug->type,
                            'name'       => $drug->name,
                            'dose'       => $drug->dose,
                            'unit'       => $drug->unit,
                            'frequency'  => $drug->frequency,
                            'duration'   => $drug->duration,
                            'created_by' => Auth::id(),
                        ]);
                    }
                }
            }
        }

        return redirect()->route('clinical.doctor.visit-page', [$unitView->id, $visit->id]);
    }

    // -----------------------------------------------------------------------
    // GET /{unitView}/doctor/visit/{visit}  — today's visit page
    // -----------------------------------------------------------------------
    public function visitPage(UnitView $unitView, ClinicVisit $visit)
    {
        $this->authorizeView($unitView);
        abort_if($visit->unit_id !== $unitView->unit_id, 403);

        $visit->load('patient.allergies', 'note', 'bpReadings', 'investigations', 'drugs.createdBy', 'drugs.updatedBy', 'drugChanges.user');
        $unitView->load('unit.institution', 'unit.unitTemplate', 'viewTemplate');

        $pageTitle = $visit->patient->name . ' — Visit #' . $visit->visit_number;

        if (optional($unitView->unit->unitTemplate)->code === 'GMC') {
            $bpReadings = $visit->bpReadings->map(fn($r) => [
                'id'          => $r->id,
                'systolic'    => (int) $r->systolic,
                'diastolic'   => (int) $r->diastolic,
                'recorded_at' => $r->recorded_at->format('Y-m-d\TH:i'),
                'source'      => 'doctor',
            ])->toArray();

            // Prepend admission reading from clerk/nurse if present
            if ($visit->bp_systolic && $visit->bp_diastolic) {
                array_unshift($bpReadings, [
                    'id'          => null,
                    'systolic'    => (int) $visit->bp_systolic,
                    'diastolic'   => (int) $visit->bp_diastolic,
                    'recorded_at' => $visit->created_at->format('Y-m-d\TH:i'),
                    'source'      => 'admission',
                ]);
            }

            $clinicDrugs     = $visit->drugs->where('section', 'clinic')->values();
            $managementDrugs = $visit->drugs->where('section', 'management')->values();

            // Previous visits for BP trend chart, investigation history, and clinic visit summary
            $prevVisits = ClinicVisit::with(['bpReadings', 'investigations', 'note', 'drugs'])
                ->where('patient_id', $visit->patient_id)
                ->where('unit_id',    $visit->unit_id)
                ->where('status',     'visited')
                ->where('id',         '!=', $visit->id)
                ->orderBy('visit_date')
                ->orderBy('visit_number')
                ->get();

            // Build investigation data for dual chart/table view (current + all previous visits)
            $allInvData = [];
            foreach ($prevVisits as $pv) {
                $visitLabel = 'Visit #' . $pv->visit_number . ' · ' . $pv->visit_date->format('d M Y');
                foreach ($pv->investigations as $inv) {
                    $allInvData[$inv->name][] = [
                        'id'          => null,
                        'value'       => $inv->value,
                        'recorded_at' => $inv->recorded_at->format('d M Y H:i'),
                        'sort_ts'     => $inv->recorded_at->timestamp,
                        'visit_label' => $visitLabel,
                        'current'     => false,
                        'deletable'   => false,
                    ];
                }
            }
            foreach ($visit->investigations as $inv) {
                $allInvData[$inv->name][] = [
                    'id'          => $inv->id,
                    'value'       => $inv->value,
                    'recorded_at' => $inv->recorded_at->format('d M Y H:i'),
                    'sort_ts'     => $inv->recorded_at->timestamp,
                    'visit_label' => 'This Visit',
                    'current'     => true,
                    'deletable'   => $visit->status !== 'visited',
                ];
            }
            foreach ($allInvData as $name => &$readings) {
                usort($readings, fn($a, $b) => $a['sort_ts'] <=> $b['sort_ts']);
            }
            unset($readings);

            // For recurrent clinic visits, load drug changes from all visits of this patient in this unit
            if ($visit->category === 'recurrent_clinic_visit') {
                $allVisitIds = ClinicVisit::where('patient_id', $visit->patient_id)
                    ->where('unit_id', $visit->unit_id)
                    ->pluck('id');

                $allDrugChanges = VisitDrugChange::with('user')
                    ->whereIn('visit_id', $allVisitIds)
                    ->orderByDesc('created_at')
                    ->get();
            } else {
                $allDrugChanges = $visit->drugChanges;
            }

            $note = $visit->note;

            return view('clinical.gmc.doctor.visit', compact(
                'unitView', 'visit', 'pageTitle',
                'bpReadings', 'clinicDrugs', 'managementDrugs',
                'prevVisits', 'note', 'allDrugChanges', 'allInvData'
            ));
        }

        return view('clinical.doctor.today_visit', compact('unitView', 'visit', 'pageTitle'));
    }

    // -----------------------------------------------------------------------
    // PATCH /{unitView}/visit/{visit}/notes  — auto-save history notes
    // -----------------------------------------------------------------------
    /**
     * Persist visit note fields. Only writes fields that are present in $data.
     */
    private function persistNoteFields(ClinicVisit $visit, array $data): void
    {
        $allowed = [
            'presenting_complaints', 'complaint_durations',
            'past_medical_history',  'past_surgical_history',
            'social_history',        'menstrual_history',
            'general_looking',       'pulse_rate',
            'cardiology_findings',   'respiratory_findings',
            'abdominal_findings',    'neurological_findings',
            'dermatological_findings', 'management_instruction',
        ];

        $toSave = array_intersect_key($data, array_flip($allowed));

        if (empty($toSave)) {
            return;
        }

        try {
            VisitNote::updateOrCreate(['visit_id' => $visit->id], $toSave);
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            // Concurrent requests (e.g. multiple beforeunload saves) can race to INSERT.
            // Must use a model instance so Eloquent's array→JSON casts are applied.
            $existing = VisitNote::where('visit_id', $visit->id)->first();
            if ($existing) {
                $existing->fill($toSave)->save();
            }
        }
    }

    public function saveNotes(Request $request, UnitView $unitView, ClinicVisit $visit)
    {
        $this->authorizeView($unitView);
        abort_if($visit->unit_id !== $unitView->unit_id, 403);

        $allRules = [
            'presenting_complaints'   => 'nullable|array',
            'complaint_durations'     => 'nullable|array',
            'past_medical_history'    => 'nullable|array',
            'past_surgical_history'   => 'nullable|array',
            'social_history'          => 'nullable|array',
            'menstrual_history'       => 'nullable|array',
            'general_looking'         => 'nullable|array',
            'pulse_rate'              => 'nullable|integer|min:0|max:350',
            'cardiology_findings'     => 'nullable|array',
            'respiratory_findings'    => 'nullable|array',
            'abdominal_findings'      => 'nullable|array',
            'neurological_findings'   => 'nullable|array',
            'dermatological_findings' => 'nullable|array',
            'management_instruction'  => 'nullable|array',
        ];

        // Only validate fields that were actually sent — absent fields must not
        // overwrite existing data with null.
        $sentKeys    = array_keys($request->all());
        $activeRules = array_intersect_key($allRules, array_flip($sentKeys));

        if (empty($activeRules)) {
            return response()->json(['ok' => true]);
        }

        $validated = $request->validate($activeRules);
        $this->persistNoteFields($visit, $validated);

        return response()->json(['ok' => true]);
    }

    // -----------------------------------------------------------------------
    // POST /{unitView}/patient/{patient}/allergy  — add allergy
    // -----------------------------------------------------------------------
    public function addAllergy(Request $request, UnitView $unitView, Patient $patient)
    {
        $this->authorizeView($unitView);

        $request->validate(['allergen' => 'required|string|max:255']);

        $allergy = PatientAllergy::firstOrCreate([
            'patient_id' => $patient->id,
            'allergen'   => trim($request->allergen),
        ]);

        return response()->json(['ok' => true, 'id' => $allergy->id]);
    }

    // -----------------------------------------------------------------------
    // DELETE /{unitView}/patient/{patient}/allergy/{allergy}  — remove allergy
    // -----------------------------------------------------------------------
    public function removeAllergy(UnitView $unitView, Patient $patient, PatientAllergy $allergy)
    {
        $this->authorizeView($unitView);
        abort_if($allergy->patient_id !== $patient->id, 403);

        $allergy->delete();

        return response()->json(['ok' => true]);
    }

    // -----------------------------------------------------------------------
    // POST /{unitView}/doctor/visit/{visit}/end  — in_progress → visited
    // -----------------------------------------------------------------------
    public function endVisit(Request $request, UnitView $unitView, ClinicVisit $visit)
    {
        $this->authorizeView($unitView);
        abort_if($visit->unit_id !== $unitView->unit_id, 403);

        // Save any notes data bundled with the form submission.
        // Wrapped in try-catch so a failed note save never blocks the status update.
        try {
            $notesJson = $request->input('notes_json');
            if ($notesJson) {
                $notesData = json_decode($notesJson, true);
                if (is_array($notesData)) {
                    // Coerce array fields to arrays; pulse_rate to int/null.
                    $arrayFields = [
                        'presenting_complaints', 'complaint_durations',
                        'past_medical_history',  'past_surgical_history',
                        'social_history',        'menstrual_history',
                        'general_looking',       'cardiology_findings',
                        'respiratory_findings',  'abdominal_findings',
                        'neurological_findings', 'dermatological_findings',
                        'management_instruction',
                    ];
                    $clean = [];
                    foreach ($arrayFields as $field) {
                        if (array_key_exists($field, $notesData)) {
                            $clean[$field] = is_array($notesData[$field]) ? $notesData[$field] : [];
                        }
                    }
                    if (array_key_exists('pulse_rate', $notesData)) {
                        $pr = $notesData['pulse_rate'];
                        $clean['pulse_rate'] = is_numeric($pr) ? (int) $pr : null;
                    }
                    $this->persistNoteFields($visit, $clean);
                }
            }
        } catch (\Exception $e) {
            // Note save failed (e.g. a pending migration). Log and continue so the
            // visit status update always completes.
            \Illuminate\Support\Facades\Log::warning('endVisit: note save failed', [
                'visit_id' => $visit->id,
                'error'    => $e->getMessage(),
            ]);
        }

        if (in_array($visit->status, ['waiting', 'in_progress'])) {
            $visit->load('patient');
            $visit->update(['status' => 'visited']);
        }

        return redirect()
            ->to(route('clinical.doctor.patient-history', [$unitView->id, $visit->patient_id]) . '?visit=' . $visit->id)
            ->with('success', 'Visit for ' . $visit->patient->name . ' has been closed.');
    }

    // -----------------------------------------------------------------------
    // GET /{unitView}/doctor/visit/{visit}/summary  — past visit (placeholder)
    // -----------------------------------------------------------------------
    public function visitSummary(UnitView $unitView, ClinicVisit $visit)
    {
        $this->authorizeView($unitView);
        abort_if($visit->unit_id !== $unitView->unit_id, 403);

        $visit->load('patient', 'note', 'bpReadings', 'investigations', 'drugs');
        $unitView->load('unit.institution', 'viewTemplate');

        $clinicDrugs     = $visit->drugs->where('section', 'clinic')->values();
        $managementDrugs = $visit->drugs->where('section', 'management')->values();

        $pageTitle = 'Visit #' . $visit->visit_number . ' — ' . $visit->patient->name;
        return view('clinical.doctor.visit_summary',
            compact('unitView', 'visit', 'pageTitle', 'clinicDrugs', 'managementDrugs'));
    }

    // -----------------------------------------------------------------------
    // POST /{unitView}/visit/{visit}/bp
    // -----------------------------------------------------------------------
    public function storeBpReading(Request $request, UnitView $unitView, ClinicVisit $visit)
    {
        $this->authorizeView($unitView);
        abort_if($visit->unit_id !== $unitView->unit_id, 403);

        $request->validate([
            'systolic'  => 'required|integer|min:40|max:300',
            'diastolic' => 'required|integer|min:20|max:200',
        ]);

        // Accept either a combined recorded_at (AJAX) or separate bp_date + bp_time (form POST)
        if ($request->has('bp_date')) {
            $recordedAt = $request->input('bp_date') . ' ' . ($request->input('bp_time', '00:00')) . ':00';
        } else {
            $request->validate(['recorded_at' => 'required|date']);
            $recordedAt = $request->input('recorded_at');
        }

        $r = $visit->bpReadings()->create([
            'systolic'    => $request->input('systolic'),
            'diastolic'   => $request->input('diastolic'),
            'recorded_at' => $recordedAt,
            'recorded_by' => Auth::id(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'ok'      => true,
                'reading' => [
                    'id'          => $r->id,
                    'systolic'    => (int) $r->systolic,
                    'diastolic'   => (int) $r->diastolic,
                    'recorded_at' => $r->recorded_at->format('Y-m-d\TH:i'),
                    'source'      => 'doctor',
                ],
            ]);
        }

        return redirect()->back();
    }

    // -----------------------------------------------------------------------
    // PATCH /{unitView}/bp/{bpReading}
    // -----------------------------------------------------------------------
    public function updateBpReading(Request $request, UnitView $unitView, BloodPressureReading $bpReading)
    {
        $this->authorizeView($unitView);
        abort_if($bpReading->visit->unit_id !== $unitView->unit_id, 403);

        $v = $request->validate([
            'systolic'    => 'sometimes|integer|min:40|max:300',
            'diastolic'   => 'sometimes|integer|min:20|max:200',
            'recorded_at' => 'sometimes|date',
        ]);

        $bpReading->update($v);

        return response()->json(['ok' => true]);
    }

    // -----------------------------------------------------------------------
    // DELETE /{unitView}/bp/{bpReading}
    // -----------------------------------------------------------------------
    public function deleteBpReading(UnitView $unitView, BloodPressureReading $bpReading)
    {
        $this->authorizeView($unitView);
        abort_if($bpReading->visit->unit_id !== $unitView->unit_id, 403);

        $bpReading->delete();

        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->back();
    }

    // -----------------------------------------------------------------------
    // POST /{unitView}/visit/{visit}/investigation
    // -----------------------------------------------------------------------
    public function storeInvestigation(Request $request, UnitView $unitView, ClinicVisit $visit)
    {
        $this->authorizeView($unitView);
        abort_if($visit->unit_id !== $unitView->unit_id, 403);

        $allowed = ['FBS', 'HbA1c', 'Serum creatinine', 'Total cholesterol', 'TSH'];

        $data = $request->validate([
            'name'  => 'required|string|in:' . implode(',', $allowed),
            'value' => 'required|string|max:100',
        ]);

        if ($request->has('inv_date')) {
            $recordedAt = $request->input('inv_date') . ' ' . ($request->input('inv_time', '00:00')) . ':00';
        } else {
            $request->validate(['recorded_at' => 'required|date']);
            $recordedAt = $request->input('recorded_at');
        }

        $inv = $visit->investigations()->create([
            'name'        => $data['name'],
            'value'       => $data['value'],
            'recorded_at' => $recordedAt,
            'recorded_by' => Auth::id(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'ok'            => true,
                'investigation' => [
                    'id'          => $inv->id,
                    'name'        => $inv->name,
                    'value'       => $inv->value,
                    'recorded_at' => $inv->recorded_at->format('d M Y H:i'),
                ],
            ]);
        }

        return redirect()->back();
    }

    // -----------------------------------------------------------------------
    // DELETE /{unitView}/investigation/{investigation}
    // -----------------------------------------------------------------------
    public function deleteInvestigation(UnitView $unitView, Investigation $investigation)
    {
        $this->authorizeView($unitView);
        abort_if($investigation->visit->unit_id !== $unitView->unit_id, 403);

        $investigation->delete();

        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->back();
    }

    // -----------------------------------------------------------------------
    // POST /{unitView}/visit/{visit}/drug
    // -----------------------------------------------------------------------
    public function storeDrug(Request $request, UnitView $unitView, ClinicVisit $visit)
    {
        $this->authorizeView($unitView);
        abort_if($visit->unit_id !== $unitView->unit_id, 403);

        $data = $request->validate([
            'section'   => 'nullable|in:clinic,management',
            'type'      => 'required|in:Oral,S/C,IM,IV,S/L,Syrup,MDI,DPI,Suppository,LA',
            'name'      => 'required|string|max:200',
            'dose'      => 'required|string|max:50',
            'unit'      => 'required|in:mg,g,mcg,ml,tabs,item',
            'frequency' => 'required|in:mane,nocte,bd,tds,daily,EOD,SOS',
            'duration'  => 'nullable|string|max:50',
        ]);

        $data['section'] = $data['section'] ?? 'clinic';

        $drug = $visit->drugs()->create([...$data, 'created_by' => Auth::id()]);

        VisitDrugChange::create([
            'visit_id'   => $visit->id,
            'drug_id'    => $drug->id,
            'user_id'    => Auth::id(),
            'action'     => 'added',
            'new_values' => $data,
        ]);

        if ($request->expectsJson()) {
            $drug->load('createdBy');
            return response()->json([
                'ok'   => true,
                'drug' => [
                    'id'        => $drug->id,
                    'section'   => $drug->section,
                    'type'      => $drug->type,
                    'name'      => $drug->name,
                    'dose'      => $drug->dose,
                    'unit'      => $drug->unit,
                    'frequency' => $drug->frequency,
                    'duration'  => $drug->duration,
                    'by'        => optional($drug->createdBy)->name ?? '',
                    'change'    => VisitDrugChange::where('visit_id', $visit->id)
                                    ->where('drug_id', $drug->id)
                                    ->latest()->first()->toSentence(),
                ],
            ]);
        }

        return redirect()->back();
    }

    // -----------------------------------------------------------------------
    // PATCH /{unitView}/drug/{drug}
    // -----------------------------------------------------------------------
    public function updateDrug(Request $request, UnitView $unitView, VisitDrug $drug)
    {
        $this->authorizeView($unitView);
        abort_if($drug->visit->unit_id !== $unitView->unit_id, 403);

        $data = $request->validate([
            'type'      => 'required|in:Oral,S/C,IM,IV,S/L,Syrup,MDI,DPI,Suppository,LA',
            'name'      => 'required|string|max:200',
            'dose'      => 'required|string|max:50',
            'unit'      => 'required|in:mg,g,mcg,ml,tabs,item',
            'frequency' => 'required|in:mane,nocte,bd,tds,daily,EOD,SOS',
            'duration'  => 'nullable|string|max:50',
        ]);

        $old = $drug->only(['type', 'name', 'dose', 'unit', 'frequency']);

        $drug->update([...$data, 'updated_by' => Auth::id()]);

        $change = VisitDrugChange::create([
            'visit_id'   => $drug->visit_id,
            'drug_id'    => $drug->id,
            'user_id'    => Auth::id(),
            'action'     => 'edited',
            'old_values' => $old,
            'new_values' => $data,
        ]);

        return response()->json([
            'ok'     => true,
            'drug'   => [
                'id'        => $drug->id,
                'section'   => $drug->section,
                'type'      => $drug->type,
                'name'      => $drug->name,
                'dose'      => $drug->dose,
                'unit'      => $drug->unit,
                'frequency' => $drug->frequency,
                'duration'  => $drug->duration,
            ],
            'change' => $change->toSentence(),
        ]);
    }

    // -----------------------------------------------------------------------
    // DELETE /{unitView}/drug/{drug}
    // -----------------------------------------------------------------------
    public function deleteDrug(UnitView $unitView, VisitDrug $drug)
    {
        $this->authorizeView($unitView);
        abort_if($drug->visit->unit_id !== $unitView->unit_id, 403);

        $old = $drug->only(['type', 'name', 'dose', 'unit', 'frequency']);

        $change = VisitDrugChange::create([
            'visit_id'   => $drug->visit_id,
            'drug_id'    => $drug->id,
            'user_id'    => Auth::id(),
            'action'     => 'deleted',
            'old_values' => $old,
        ]);

        $drug->delete();

        if (request()->expectsJson()) {
            return response()->json(['ok' => true, 'change' => $change->toSentence()]);
        }

        return redirect()->back();
    }

    // -----------------------------------------------------------------------
    // GET /{unitView}/drug-stock-check?drug=name
    // Check pharmacy stock for a drug name across the institution's pharmacist views
    // -----------------------------------------------------------------------
    public function drugStockCheck(Request $request, UnitView $unitView)
    {
        $this->authorizeView($unitView);

        $drugName      = trim($request->input('drug', ''));
        $institutionId = $unitView->unit->institution_id;

        if (!$drugName) {
            return response()->json(['in_stock' => false, 'remaining' => 0]);
        }

        $stock = PharmacyStock::whereHas('unitView.unit', fn($q) => $q->where('institution_id', $institutionId))
            ->whereRaw('LOWER(drug_name) = LOWER(?)', [$drugName])
            ->where('is_out_of_stock', false)
            ->where('remaining', '>', 0)
            ->sum('remaining');

        return response()->json([
            'in_stock'  => $stock > 0,
            'remaining' => (int) $stock,
        ]);
    }
}
