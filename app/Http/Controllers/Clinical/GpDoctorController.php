<?php

namespace App\Http\Controllers\Clinical;

use App\Http\Controllers\Controller;
use App\Models\ClinicVisit;
use App\Models\PharmacyRestockLog;
use App\Models\PharmacyStock;
use App\Models\UnitView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GpDoctorController extends Controller
{
    private function authorizeView(UnitView $unitView): void
    {
        if (!Auth::user()->views->contains('id', $unitView->id)) {
            abort(403);
        }
    }

    private function getInstId(UnitView $unitView): int
    {
        return $unitView->unit->institution_id;
    }

    /** All unit_view IDs in this institution (covers all pharmacist views). */
    private function getPvIds(int $institutionId): array
    {
        return UnitView::whereHas('unit', fn($q) =>
            $q->where('institution_id', $institutionId)
        )->pluck('id')->toArray();
    }

    // -----------------------------------------------------------------------
    // GET /{unitView}/gp-doctor/summary
    // -----------------------------------------------------------------------
    public function summary(UnitView $unitView)
    {
        $this->authorizeView($unitView);
        $instId = $this->getInstId($unitView);
        $pvIds  = $this->getPvIds($instId);
        $today  = now()->toDateString();

        $stockQ = PharmacyStock::whereIn('unit_view_id', $pvIds);

        $totalDrugs     = (clone $stockQ)->count();
        $totalRemaining = (clone $stockQ)->sum('remaining');
        $lowCount       = (clone $stockQ)->nearOutOfStock()->count();
        $oosCount       = (clone $stockQ)->outOfStock()->count();
        $expiringCount  = (clone $stockQ)->expiringSoon(30)->count();
        $expiredCount   = (clone $stockQ)->whereNotNull('expiry_date')
                             ->whereDate('expiry_date', '<', $today)->count();
        $okCount        = (clone $stockQ)
                             ->where('is_out_of_stock', false)
                             ->where('remaining', '>', 0)
                             ->whereColumn('remaining', '>', 'low_stock_threshold')
                             ->where(fn($q) => $q->whereNull('expiry_date')
                                 ->orWhereDate('expiry_date', '>=', $today))->count();

        $visitsQ        = ClinicVisit::where('institution_id', $instId)->where('visit_date', $today);
        $todayDispensed = (clone $visitsQ)->where('status', 'dispensed')->count();
        $queuePending   = (clone $visitsQ)->where('status', 'visited')->count();

        // Top 10 consumed drugs over the last 7 days
        $topDrugs = DB::table('prescription_dispensings as pd')
            ->join('pharmacy_stock as ps', 'pd.stock_id', '=', 'ps.id')
            ->join('visit_drugs as vd',    'pd.visit_drug_id', '=', 'vd.id')
            ->whereIn('ps.unit_view_id', $pvIds)
            ->where('pd.status', 'prescribed')
            ->whereDate('pd.dispensed_at', '>=', now()->subDays(6)->toDateString())
            ->groupBy('vd.name')
            ->select('vd.name as drug_name', DB::raw('SUM(pd.quantity_dispensed) as total_qty'))
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        return response()->json([
            'total_drugs'     => $totalDrugs,
            'total_remaining' => $totalRemaining,
            'low_count'       => $lowCount,
            'oos_count'       => $oosCount,
            'expiring_count'  => $expiringCount,
            'expired_count'   => $expiredCount,
            'ok_count'        => $okCount,
            'today_dispensed' => $todayDispensed,
            'queue_pending'   => $queuePending,
            'top_drugs'       => $topDrugs,
        ]);
    }

    // -----------------------------------------------------------------------
    // GET /{unitView}/gp-doctor/stock?filter=all|low|oos|expiring
    // -----------------------------------------------------------------------
    public function stock(Request $request, UnitView $unitView)
    {
        $this->authorizeView($unitView);
        $pvIds  = $this->getPvIds($this->getInstId($unitView));
        $filter = $request->input('filter', 'all');

        $query = PharmacyStock::whereIn('unit_view_id', $pvIds)->orderBy('drug_name');
        match ($filter) {
            'expiring' => $query->expiringSoon(30),
            'low'      => $query->nearOutOfStock(),
            'oos'      => $query->outOfStock(),
            default    => null,
        };

        $stocks = $query->get()->map(fn($s) => [
            'drug_name'         => $s->drug_name,
            'initial_amount'    => $s->initial_amount,
            'remaining'         => $s->remaining,
            'expiry_display'    => $s->expiry_date?->format('d M Y'),
            'days_until_expiry' => $s->days_until_expiry,
            'stock_status'      => $s->stock_status,
            'notes'             => $s->notes,
        ]);

        return response()->json(compact('stocks'));
    }

    // -----------------------------------------------------------------------
    // GET /{unitView}/gp-doctor/dispensing?from=&to=
    // -----------------------------------------------------------------------
    public function dispensing(Request $request, UnitView $unitView)
    {
        $this->authorizeView($unitView);
        $instId = $this->getInstId($unitView);
        $from   = $request->input('from', now()->toDateString());
        $to     = $request->input('to',   now()->toDateString());

        $records = DB::table('prescription_dispensings as pd')
            ->join('visit_drugs as vd',   'pd.visit_drug_id', '=', 'vd.id')
            ->join('clinic_visits as cv',  'pd.visit_id',      '=', 'cv.id')
            ->join('patients as p',        'cv.patient_id',    '=', 'p.id')
            ->leftJoin('users as u',       'pd.dispensed_by',  '=', 'u.id')
            ->where('cv.institution_id', $instId)
            ->whereRaw('DATE(pd.dispensed_at) BETWEEN ? AND ?', [$from, $to])
            ->select(
                DB::raw('DATE_FORMAT(pd.dispensed_at, "%d %b %Y %H:%i") as dispensed_at'),
                'cv.visit_number',
                'cv.category',
                'p.name as patient_name',
                'vd.name as drug_name',
                'pd.quantity_dispensed',
                'pd.status',
                DB::raw('COALESCE(u.name, "—") as pharmacist')
            )
            ->orderByDesc('pd.dispensed_at')
            ->limit(500)
            ->get();

        return response()->json(compact('records'));
    }

    // -----------------------------------------------------------------------
    // GET /{unitView}/gp-doctor/log?from=&to=
    // -----------------------------------------------------------------------
    public function log(Request $request, UnitView $unitView)
    {
        $this->authorizeView($unitView);
        $instId = $this->getInstId($unitView);
        $pvIds  = $this->getPvIds($instId);
        $from   = $request->input('from', now()->toDateString());
        $to     = $request->input('to',   now()->toDateString());

        $consumption = DB::table('prescription_dispensings as pd')
            ->join('pharmacy_stock as ps', 'pd.stock_id', '=', 'ps.id')
            ->join('visit_drugs as vd',    'pd.visit_drug_id', '=', 'vd.id')
            ->whereIn('ps.unit_view_id', $pvIds)
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

        $restock = PharmacyRestockLog::whereIn('unit_view_id', $pvIds)
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

        $expired = PharmacyStock::whereIn('unit_view_id', $pvIds)
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
}
