<?php

namespace App\Http\Controllers\Clinical;

use App\Http\Controllers\Controller;
use App\Models\UnitView;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $assignedViews = Auth::user()
            ->views()
            ->with('unit.institution', 'viewTemplate')
            ->get();

        return view('clinical.dashboard', compact('assignedViews'));
    }

    public function show(UnitView $unitView)
    {
        if (!Auth::user()->views->contains('id', $unitView->id)) {
            abort(403, 'You are not assigned to this view.');
        }

        $unitView->load('unit.institution', 'viewTemplate');

        $unit         = $unitView->unit;
        $viewTemplate = $unitView->viewTemplate;
        $pageTitle    = $viewTemplate->name . ' — ' . $unit->name;

        return view($viewTemplate->blade_path, compact('unitView', 'unit', 'viewTemplate', 'pageTitle'));
    }
}
