<?php

namespace App\Http\Controllers\Clinical;

use App\Http\Controllers\Controller;
use App\Models\ClinicVisit;
use App\Models\PharmacyRestockLog;
use App\Models\PharmacyStock;
use App\Models\PrescriptionDispensing;
use App\Models\UnitView;
use App\Models\VisitDrug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PharmacistController extends Controller
{
    private function authorizeView(UnitView $unitView): void
    {
        if (!Auth::user()->views->contains('id', $unitView->id)) {
            abort(403);
        }
    }

    // -----------------------------------------------------------------------
    // GET /{unitView}/pharmacist/queue
    // Today's pharmacy queue: visited + dispensed visits for the whole
    // institution (all units/clinics), grouped by category.
    // -----------------------------------------------------------------------
    public function queue(UnitView $unitView)
    {
        $this->authorizeView($unitView);

        $institutionId = $unitView->unit->institution_id;
        $today         = now()->toDateString();

        $visits = ClinicVisit::with('patient', 'unit')
            ->where('institution_id', $institutionId)
            ->where('visit_date', $today)
            ->whereIn('status', ['visited', 'dispensed'])
            ->orderBy('visit_number')
            ->get();

        $grouped = $visits->groupBy('category');

        $catOrder = ['opd', 'new_clinic_visit', 'recurrent_clinic_visit', 'urgent'];
        $queue    = [];

        foreach ($catOrder as $cat) {
            $queue[$cat] = ($grouped->get($cat, collect()))->map(fn($v) => [
                'id'            => $v->id,
                'visit_number'  => $v->visit_number,
                'patient_name'  => $v->patient->name,
                'clinic_number' => $v->clinic_number,
                'opd_number'    => $v->opd_number,
                'category'      => $v->category,
                'status'        => $v->status,
                'dispensed'     => $v->status === 'dispensed',
                'unit_name'     => $v->unit->name ?? '',
            ])->values();
        }

        $counts = [
            'total'                  => $visits->count(),
            'pending'                => $visits->where('status', 'visited')->count(),
            'dispensed'              => $visits->where('status', 'dispensed')->count(),
            'opd'                    => ($grouped->get('opd', collect()))->count(),
            'new_clinic_visit'       => ($grouped->get('new_clinic_visit', collect()))->count(),
            'recurrent_clinic_visit' => ($grouped->get('recurrent_clinic_visit', collect()))->count(),
            'urgent'                 => ($grouped->get('urgent', collect()))->count(),
        ];

        return response()->json(compact('queue', 'counts'));
    }

    // -----------------------------------------------------------------------
    // GET /{unitView}/pharmacist/search?q=
    // Search today's visits by queue number, clinic/OPD number, or patient name
    // -----------------------------------------------------------------------
    public function searchPatient(Request $request, UnitView $unitView)
    {
        $this->authorizeView($unitView);

        $q = trim($request->input('q', ''));

        if (strlen($q) < 1) {
            return response()->json([]);
        }

        $institutionId = $unitView->unit->institution_id;
        $today         = now()->toDateString();

        $visits = ClinicVisit::with('patient', 'unit')
            ->where('institution_id', $institutionId)
            ->where('visit_date', $today)
            ->where(function ($query) use ($q) {
                $query->where('visit_number', $q)
                      ->orWhere('clinic_number', 'like', "%{$q}%")
                      ->orWhere('opd_number', 'like', "%{$q}%")
                      ->orWhereHas('patient', fn($pq) => $pq->where('name', 'like', "%{$q}%"));
            })
            ->limit(15)
            ->get();

        return response()->json($visits->map(fn($v) => [
            'id'            => $v->id,
            'visit_number'  => $v->visit_number,
            'patient_name'  => $v->patient->name,
            'clinic_number' => $v->clinic_number,
            'opd_number'    => $v->opd_number,
            'category'      => $v->category,
            'status'        => $v->status,
            'dispensed'     => $v->status === 'dispensed',
            'unit_name'     => $v->unit->name ?? '',
        ])->values());
    }

    // -----------------------------------------------------------------------
    // GET /{unitView}/pharmacist/visit/{visit}
    // Full prescription receipt for one visit
    // -----------------------------------------------------------------------
    public function visitDetail(UnitView $unitView, ClinicVisit $visit)
    {
        $this->authorizeView($unitView);
        // Allow access to any visit within the same institution (central pharmacy)
        abort_if($visit->institution_id !== $unitView->unit->institution_id, 403);

        $visit->load('patient.allergies', 'note', 'drugs.dispensings', 'unit');

        $mapDrug = function (VisitDrug $d) use ($unitView) {
            // Find best-match stock item (FEFO: soonest expiry first, then no-expiry)
            $stock = PharmacyStock::where('unit_view_id', $unitView->id)
                ->whereRaw('LOWER(drug_name) = LOWER(?)', [$d->name])
                ->where('is_out_of_stock', false)
                ->where('remaining', '>', 0)
                ->orderByRaw('expiry_date IS NULL ASC')
                ->orderBy('expiry_date')
                ->first();

            $dispensing = $d->dispensings->first();

            return [
                'id'               => $d->id,
                'section'          => $d->section,
                'type'             => $d->type,
                'name'             => $d->name,
                'dose'             => $d->dose,
                'unit'             => $d->unit,
                'frequency'        => $d->frequency,
                'duration'         => $d->duration,
                'formatted'        => VisitDrug::formatDrug($d->type, $d->name, $d->dose, $d->unit, $d->frequency),
                'stock_id'         => $stock?->id,
                'in_stock'         => $stock !== null,
                'stock_remaining'  => $stock?->remaining,
                'dispensed_status' => $dispensing?->status,
                'dispensed_qty'    => $dispensing?->quantity_dispensed,
            ];
        };

        $drugs = $visit->drugs->map($mapDrug);

        $catLabels = [
            'opd'                    => 'OPD',
            'new_clinic_visit'       => 'New Clinic Visit',
            'recurrent_clinic_visit' => 'Recurrent Clinic Visit',
            'urgent'                 => 'Urgent',
        ];

        $note = $visit->note;

        return response()->json([
            'visit' => [
                'id'            => $visit->id,
                'visit_number'  => $visit->visit_number,
                'category'      => $visit->category,
                'cat_label'     => $catLabels[$visit->category] ?? $visit->category,
                'status'        => $visit->status,
                'dispensed'     => $visit->status === 'dispensed',
                'clinic_number' => $visit->clinic_number,
                'opd_number'    => $visit->opd_number,
                'visit_date'    => $visit->visit_date->format('d M Y'),
                'height'        => $visit->height,
                'weight'        => $visit->weight,
                'unit_name'     => $visit->unit->name ?? '',
            ],
            'patient' => [
                'id'       => $visit->patient->id,
                'name'     => $visit->patient->name,
                'age'      => $visit->patient->computed_age,
                'gender'   => ucfirst($visit->patient->gender ?? ''),
                'phn'      => $visit->patient->phn,
                'allergies'=> $visit->patient->allergies->pluck('allergen')->values(),
            ],
            'note' => $note ? [
                'presenting_complaints'  => $note->presenting_complaints ?? [],
                'past_medical_history'   => $note->past_medical_history ?? [],
                'general_looking'        => $note->general_looking ?? [],
                'cardiology_findings'    => $note->cardiology_findings ?? [],
                'respiratory_findings'   => $note->respiratory_findings ?? [],
                'abdominal_findings'     => $note->abdominal_findings ?? [],
                'management_instruction' => $note->management_instruction ?? [],
            ] : null,
            'clinic_drugs'     => $drugs->where('section', 'clinic')->values(),
            'management_drugs' => $drugs->where('section', 'management')->values(),
        ]);
    }

    // -----------------------------------------------------------------------
    // POST /{unitView}/pharmacist/visit/{visit}/dispense
    // Body: { drugs: [{ drug_id, status, qty, stock_id }] }
    // -----------------------------------------------------------------------
    public function dispense(Request $request, UnitView $unitView, ClinicVisit $visit)
    {
        $this->authorizeView($unitView);
        abort_if($visit->institution_id !== $unitView->unit->institution_id, 403);
        abort_if($visit->status === 'dispensed', 422, 'This prescription is already dispensed.');

        $data = $request->validate([
            'drugs'            => 'present|array',
            'drugs.*.drug_id'  => 'required|integer|exists:visit_drugs,id',
            'drugs.*.status'   => 'required|in:prescribed,os',
            'drugs.*.qty'      => 'nullable|integer|min:1',
            'drugs.*.stock_id' => 'nullable|integer|exists:pharmacy_stock,id',
        ]);

        DB::transaction(function () use ($data, $visit, $unitView) {
            $now = now();

            foreach ($data['drugs'] as $item) {
                $stockId = $item['stock_id'] ?? null;
                $status  = $item['status'];
                $qty     = (int) ($item['qty'] ?? 1);

                // Deduct from stock when prescribed
                if ($status === 'prescribed' && $stockId) {
                    $stock = PharmacyStock::find($stockId);
                    if ($stock && $stock->unit_view_id === $unitView->id && !$stock->is_out_of_stock) {
                        $stock->update([
                            'remaining'  => max(0, $stock->remaining - $qty),
                            'updated_by' => Auth::id(),
                        ]);
                    }
                }

                PrescriptionDispensing::updateOrCreate(
                    ['visit_drug_id' => $item['drug_id']],
                    [
                        'visit_id'          => $visit->id,
                        'stock_id'          => ($status === 'prescribed') ? $stockId : null,
                        'status'            => $status,
                        'quantity_dispensed'=> ($status === 'prescribed') ? $qty : 0,
                        'dispensed_by'      => Auth::id(),
                        'dispensed_at'      => $now,
                    ]
                );
            }

            $visit->update(['status' => 'dispensed']);
        });

        return response()->json(['ok' => true, 'message' => 'Prescription completed successfully.']);
    }

    // -----------------------------------------------------------------------
    // GET /{unitView}/pharmacist/stock?filter=all|expiring|low|oos
    // -----------------------------------------------------------------------
    public function stockIndex(Request $request, UnitView $unitView)
    {
        $this->authorizeView($unitView);

        $filter = $request->input('filter', 'all');

        $query = PharmacyStock::where('unit_view_id', $unitView->id)->orderBy('drug_name');

        match ($filter) {
            'expiring' => $query->expiringSoon(30),
            'low'      => $query->nearOutOfStock(),
            'oos'      => $query->outOfStock(),
            default    => null,
        };

        $stocks = $query->get()->map(fn($s) => [
            'id'                  => $s->id,
            'drug_name'           => $s->drug_name,
            'initial_amount'      => $s->initial_amount,
            'remaining'           => $s->remaining,
            'expiry_date'         => $s->expiry_date?->format('Y-m-d'),
            'expiry_display'      => $s->expiry_date?->format('d M Y'),
            'days_until_expiry'   => $s->days_until_expiry,
            'is_out_of_stock'     => $s->is_out_of_stock,
            'stock_status'        => $s->stock_status,
            'low_stock_threshold' => $s->low_stock_threshold,
            'notes'               => $s->notes,
        ]);

        return response()->json(compact('stocks'));
    }

    // -----------------------------------------------------------------------
    // POST /{unitView}/pharmacist/stock
    // -----------------------------------------------------------------------
    public function stockStore(Request $request, UnitView $unitView)
    {
        $this->authorizeView($unitView);

        $data = $request->validate([
            'drug_name'           => 'required|string|max:200',
            'initial_amount'      => 'required|numeric|min:1|max:999999',
            'expiry_date'         => 'nullable|date|after_or_equal:today',
            'low_stock_threshold' => 'nullable|numeric|min:1',
            'notes'               => 'nullable|string|max:500',
        ]);

        $stock = PharmacyStock::create([
            'unit_view_id'        => $unitView->id,
            'drug_name'           => $data['drug_name'],
            'initial_amount'      => $data['initial_amount'],
            'remaining'           => $data['initial_amount'],
            'expiry_date'         => $data['expiry_date'] ?? null,
            'is_out_of_stock'     => false,
            'low_stock_threshold' => $data['low_stock_threshold'] ?? 10,
            'notes'               => $data['notes'] ?? null,
            'created_by'          => Auth::id(),
        ]);

        PharmacyRestockLog::create([
            'unit_view_id' => $unitView->id,
            'stock_id'     => $stock->id,
            'drug_name'    => $stock->drug_name,
            'action'       => 'new_stock',
            'amount'       => $stock->initial_amount,
            'expiry_date'  => $stock->expiry_date,
            'notes'        => $stock->notes,
            'performed_by' => Auth::id(),
        ]);

        return response()->json([
            'ok'    => true,
            'stock' => [
                'id'                  => $stock->id,
                'drug_name'           => $stock->drug_name,
                'initial_amount'      => $stock->initial_amount,
                'remaining'           => $stock->remaining,
                'expiry_date'         => $stock->expiry_date?->format('Y-m-d'),
                'expiry_display'      => $stock->expiry_date?->format('d M Y'),
                'days_until_expiry'   => $stock->days_until_expiry,
                'is_out_of_stock'     => false,
                'stock_status'        => $stock->stock_status,
                'low_stock_threshold' => $stock->low_stock_threshold,
                'notes'               => $stock->notes,
            ],
        ]);
    }

    // -----------------------------------------------------------------------
    // PATCH /{unitView}/pharmacist/stock/{stock}/oos  — toggle manual OOS flag
    // -----------------------------------------------------------------------
    public function stockToggleOos(UnitView $unitView, PharmacyStock $stock)
    {
        $this->authorizeView($unitView);
        abort_if($stock->unit_view_id !== $unitView->id, 403);

        $stock->update([
            'is_out_of_stock' => !$stock->is_out_of_stock,
            'updated_by'      => Auth::id(),
        ]);

        $stock->refresh();

        return response()->json([
            'ok'             => true,
            'is_out_of_stock'=> $stock->is_out_of_stock,
            'stock_status'   => $stock->stock_status,
        ]);
    }

    // -----------------------------------------------------------------------
    // PATCH /{unitView}/pharmacist/stock/{stock}/restock
    // Body: { add_amount, expiry_date?, notes? }
    // Adds quantity to an existing stock entry (new delivery arrived).
    // -----------------------------------------------------------------------
    public function stockRestock(Request $request, UnitView $unitView, PharmacyStock $stock)
    {
        $this->authorizeView($unitView);
        abort_if($stock->unit_view_id !== $unitView->id, 403);

        $data = $request->validate([
            'add_amount'  => 'required|numeric|min:1|max:999999',
            'expiry_date' => 'nullable|date|after_or_equal:today',
            'notes'       => 'nullable|string|max:500',
        ]);

        $stock->update([
            'initial_amount'  => $stock->initial_amount + $data['add_amount'],
            'remaining'       => $stock->remaining      + $data['add_amount'],
            'expiry_date'     => $data['expiry_date'] ?? $stock->expiry_date,
            'notes'           => array_key_exists('notes', $data) ? $data['notes'] : $stock->notes,
            'is_out_of_stock' => false,
            'updated_by'      => Auth::id(),
        ]);

        $stock->refresh();

        PharmacyRestockLog::create([
            'unit_view_id' => $unitView->id,
            'stock_id'     => $stock->id,
            'drug_name'    => $stock->drug_name,
            'action'       => 'restock',
            'amount'       => (int) $data['add_amount'],
            'expiry_date'  => $data['expiry_date'] ?? null,
            'notes'        => $data['notes'] ?? null,
            'performed_by' => Auth::id(),
        ]);

        return response()->json([
            'ok'    => true,
            'stock' => [
                'id'                  => $stock->id,
                'drug_name'           => $stock->drug_name,
                'initial_amount'      => $stock->initial_amount,
                'remaining'           => $stock->remaining,
                'expiry_date'         => $stock->expiry_date?->format('Y-m-d'),
                'expiry_display'      => $stock->expiry_date?->format('d M Y'),
                'days_until_expiry'   => $stock->days_until_expiry,
                'is_out_of_stock'     => $stock->is_out_of_stock,
                'stock_status'        => $stock->stock_status,
                'low_stock_threshold' => $stock->low_stock_threshold,
                'notes'               => $stock->notes,
            ],
        ]);
    }

    // -----------------------------------------------------------------------
    // DELETE /{unitView}/pharmacist/stock/{stock}
    // -----------------------------------------------------------------------
    public function stockDestroy(UnitView $unitView, PharmacyStock $stock)
    {
        $this->authorizeView($unitView);
        abort_if($stock->unit_view_id !== $unitView->id, 403);

        $stock->delete();

        return response()->json(['ok' => true]);
    }

    // -----------------------------------------------------------------------
    // GET /{unitView}/pharmacist/log?from=YYYY-MM-DD&to=YYYY-MM-DD
    // Daily consumption, restock history, and expired stock
    // -----------------------------------------------------------------------
    public function stockLog(Request $request, UnitView $unitView)
    {
        $this->authorizeView($unitView);

        $from = $request->input('from', now()->toDateString());
        $to   = $request->input('to',   now()->toDateString());

        // Consumption: drugs dispensed from this unit's stock within the date range
        $consumption = DB::table('prescription_dispensings as pd')
            ->join('pharmacy_stock as ps', 'pd.stock_id', '=', 'ps.id')
            ->join('visit_drugs as vd',    'pd.visit_drug_id', '=', 'vd.id')
            ->where('ps.unit_view_id', $unitView->id)
            ->where('pd.status', 'prescribed')
            ->whereRaw('DATE(pd.dispensed_at) BETWEEN ? AND ?', [$from, $to])
            ->groupBy(DB::raw('DATE(pd.dispensed_at)'), 'vd.name')
            ->select(
                DB::raw('DATE(pd.dispensed_at) as date'),
                'vd.name as drug_name',
                DB::raw('SUM(pd.quantity_dispensed) as total_qty'),
                DB::raw('COUNT(*) as patient_count')
            )
            ->orderByDesc('date')
            ->orderBy('vd.name')
            ->get();

        // Restock history within the date range
        $restock = PharmacyRestockLog::where('unit_view_id', $unitView->id)
            ->whereRaw('DATE(created_at) BETWEEN ? AND ?', [$from, $to])
            ->with('performer:id,name')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($r) => [
                'drug_name'    => $r->drug_name,
                'action'       => $r->action,
                'amount'       => $r->amount,
                'expiry_date'  => $r->expiry_date?->format('d M Y'),
                'notes'        => $r->notes,
                'performed_by' => $r->performer?->name ?? '—',
                'date'         => $r->created_at->format('d M Y H:i'),
            ]);

        // Expired stock (all time — static snapshot)
        $expired = PharmacyStock::where('unit_view_id', $unitView->id)
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', now()->toDateString())
            ->orderBy('expiry_date')
            ->get()
            ->map(fn($s) => [
                'drug_name'   => $s->drug_name,
                'expiry_date' => $s->expiry_date->format('d M Y'),
                'remaining'   => $s->remaining,
            ]);

        return response()->json(compact('consumption', 'restock', 'expired'));
    }

    // -----------------------------------------------------------------------
    // GET /{unitView}/pharmacist/alerts  — badge counts for the header
    // -----------------------------------------------------------------------
    public function stockAlerts(UnitView $unitView)
    {
        $this->authorizeView($unitView);

        $base = PharmacyStock::where('unit_view_id', $unitView->id);

        return response()->json([
            'expiring_soon' => (clone $base)->expiringSoon(30)->count(),
            'near_oos'      => (clone $base)->nearOutOfStock()->count(),
            'out_of_stock'  => (clone $base)->outOfStock()->count(),
            'queue_pending' => ClinicVisit::where('institution_id', $unitView->unit->institution_id)
                                ->where('visit_date', now()->toDateString())
                                ->where('status', 'visited')
                                ->count(),
        ]);
    }
}
