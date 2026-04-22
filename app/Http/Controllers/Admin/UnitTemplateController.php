<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UnitTemplate;
use Illuminate\Http\Request;

class UnitTemplateController extends Controller
{
    public function index()
    {
        $templates = UnitTemplate::withCount('units')->orderBy('name')->get();

        return view('admin.unit-templates.index', compact('templates'));
    }

    public function store(Request $request)
    {
        $request->merge(['code' => strtoupper($request->input('code', ''))]);

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:unit_templates,code',
        ]);

        UnitTemplate::create([
            'name'      => $data['name'],
            'code'      => $data['code'],
            'is_system' => false,
        ]);

        return back()->with('success', 'Unit template "' . $data['name'] . '" created.');
    }

    public function destroy(UnitTemplate $unitTemplate)
    {
        if ($unitTemplate->is_system) {
            return back()->with('error', 'System unit templates cannot be deleted.');
        }

        if ($unitTemplate->units()->exists()) {
            return back()->with('error', 'Cannot delete — units are using this template.');
        }

        $unitTemplate->delete();

        return back()->with('success', 'Unit template deleted.');
    }
}
