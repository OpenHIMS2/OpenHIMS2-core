<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UnitTemplate;

class UnitTemplateController extends Controller
{
    public function index()
    {
        $templates = UnitTemplate::withCount('units')->orderBy('name')->get();

        return view('admin.unit-templates.index', compact('templates'));
    }
}
