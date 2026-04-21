<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DrugName;
use App\Models\DrugNameDefault;
use Illuminate\Http\Request;

class DrugManagementController extends Controller
{
    private const TYPES  = ['Oral','S/C','IM','IV','S/L','Syrup','MDI','DPI','Suppository','LA'];
    private const UNITS  = ['mg','g','mcg','ml','tabs','item'];
    private const FREQS  = ['mane','nocte','bd','tds','daily','EOD','SOS'];

    // GET /admin/drugs
    public function index()
    {
        $drugs    = DrugName::orderBy('name')->with('default')->get();
        $defaults = $drugs->filter(fn($d) => $d->default !== null)->values();

        return view('admin.drugs.index', [
            'drugs'    => $drugs,
            'defaults' => $defaults,
            'types'    => self::TYPES,
            'units'    => self::UNITS,
            'freqs'    => self::FREQS,
        ]);
    }

    // POST /admin/drugs
    public function storeDrug(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200|unique:drug_names,name',
        ]);

        DrugName::create(['name' => trim($request->name)]);

        return back()->with('success', 'Drug "' . $request->name . '" added.');
    }

    // DELETE /admin/drugs/{drug}
    public function destroyDrug(DrugName $drug)
    {
        $name = $drug->name;
        $drug->delete();

        return back()->with('success', '"' . $name . '" removed.');
    }

    // POST /admin/drugs/defaults
    public function storeDefault(Request $request)
    {
        $request->validate([
            'drug_name_id' => 'required|exists:drug_names,id',
            'type'         => 'required|in:' . implode(',', self::TYPES),
            'dose'         => 'required|string|max:50',
            'unit'         => 'required|in:' . implode(',', self::UNITS),
            'frequency'    => 'required|in:' . implode(',', self::FREQS),
            'duration'     => 'nullable|string|max:50',
        ]);

        DrugNameDefault::updateOrCreate(
            ['drug_name_id' => $request->drug_name_id],
            $request->only(['type', 'dose', 'unit', 'frequency', 'duration'])
        );

        return back()->with('success', 'Default saved.');
    }

    // DELETE /admin/drugs/defaults/{drugDefault}
    public function destroyDefault(DrugNameDefault $drugDefault)
    {
        $drugDefault->delete();

        return back()->with('success', 'Default removed.');
    }

    // GET /drugs/search?q=  — JSON autocomplete for clinical forms
    public function search(Request $request)
    {
        $q = trim($request->query('q', ''));

        $names = DrugName::when($q, fn($query) => $query->where('name', 'like', '%' . $q . '%'))
            ->orderBy('name')
            ->pluck('name');

        return response()->json($names);
    }
}
