<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\Unit;
use App\Models\UnitTemplate;
use Illuminate\Http\Request;

class UnitManagementController extends Controller
{
    public function index(Request $request)
    {
        $rootInstitutions    = Institution::whereNull('parent_id')->with('allChildren')->orderBy('name')->get();
        $selectedInstitution = null;
        $units               = collect();
        $unitTemplates       = UnitTemplate::orderBy('name')->get();

        if ($request->institution_id) {
            $selectedInstitution = Institution::findOrFail($request->institution_id);
            $units = Unit::where('institution_id', $request->institution_id)
                ->with('unitTemplate')
                ->orderBy('name')
                ->get();
        }

        return view('admin.units.index', compact('rootInstitutions', 'selectedInstitution', 'units', 'unitTemplates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'unit_number'      => 'nullable|string|max:20',
            'institution_id'   => 'required|exists:institutions,id',
            'unit_template_id' => 'required|exists:unit_templates,id',
        ]);

        Unit::create($request->only(['name', 'unit_number', 'institution_id', 'unit_template_id']));

        return redirect()->route('admin.units.index', ['institution_id' => $request->institution_id])
            ->with('success', 'Unit created successfully.');
    }

    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'unit_number' => 'nullable|string|max:20',
        ]);

        $unit->update(['unit_number' => $request->unit_number ?: null]);

        return redirect()->route('admin.units.index', ['institution_id' => $unit->institution_id])
            ->with('success', 'Unit number updated.');
    }

    public function destroy(Unit $unit)
    {
        $institutionId = $unit->institution_id;
        $unit->delete();

        return redirect()->route('admin.units.index', ['institution_id' => $institutionId])
            ->with('success', 'Unit deleted.');
    }
}
