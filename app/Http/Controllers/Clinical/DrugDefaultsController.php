<?php

namespace App\Http\Controllers\Clinical;

use App\Http\Controllers\Controller;
use App\Models\DrugName;
use Illuminate\Http\Request;

class DrugDefaultsController extends Controller
{
    /**
     * GET /drug/defaults?name=Metformin
     * Looks up the admin-configured default for the given drug name.
     * Returns { type, dose, unit, frequency } or { error }.
     */
    public function __invoke(Request $request)
    {
        $name = trim($request->query('name', ''));
        if (!$name) {
            return response()->json(['error' => 'No drug name provided'], 422);
        }

        // Exact match first, then case-insensitive contains
        $drug = DrugName::whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->with('default')
            ->first();

        if (!$drug) {
            $drug = DrugName::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($name) . '%'])
                ->with('default')
                ->orderByRaw('LENGTH(name)')  // shortest (closest) match first
                ->first();
        }

        if (!$drug || !$drug->default) {
            return response()->json(['error' => 'No defaults found'], 404);
        }

        return response()->json([
            'type'      => $drug->default->type,
            'dose'      => $drug->default->dose,
            'unit'      => $drug->default->unit,
            'frequency' => $drug->default->frequency,
            'duration'  => $drug->default->duration,
        ]);
    }
}
