<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\Unit;
use App\Models\UnitView;
use App\Models\ViewTemplate;
use Illuminate\Http\Request;

class ViewManagementController extends Controller
{
    public function index(Request $request)
    {
        $rootInstitutions    = Institution::whereNull('parent_id')->with('allChildren')->orderBy('name')->get();
        $selectedInstitution = null;
        $selectedUnit        = null;
        $units               = collect();
        $unitViews           = collect();
        $viewTemplates       = collect();

        if ($request->institution_id) {
            $selectedInstitution = Institution::findOrFail($request->institution_id);
            $units = Unit::where('institution_id', $request->institution_id)
                ->with('unitTemplate')
                ->orderBy('name')
                ->get();
        }

        if ($request->unit_id) {
            $selectedUnit = Unit::with('institution', 'unitTemplate')->findOrFail($request->unit_id);
            $unitViews    = UnitView::where('unit_id', $request->unit_id)
                ->with('viewTemplate')
                ->get();
            $viewTemplates = ViewTemplate::where('unit_template_id', $selectedUnit->unit_template_id)
                ->orderBy('name')
                ->get();
        }

        return view('admin.views.index', compact(
            'rootInstitutions', 'selectedInstitution', 'selectedUnit',
            'units', 'unitViews', 'viewTemplates'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'unit_id'          => 'required|exists:units,id',
            'view_template_id' => 'required|exists:view_templates,id',
        ]);

        UnitView::create($request->only(['name', 'unit_id', 'view_template_id']));

        $unit = Unit::find($request->unit_id);

        return redirect()->route('admin.views.index', [
            'institution_id' => $unit->institution_id,
            'unit_id'        => $request->unit_id,
        ])->with('success', 'View created successfully.');
    }

    public function destroy(UnitView $unitView)
    {
        $unit = $unitView->unit;
        $unitView->delete();

        return redirect()->route('admin.views.index', [
            'institution_id' => $unit->institution_id,
            'unit_id'        => $unit->id,
        ])->with('success', 'View deleted.');
    }
}
