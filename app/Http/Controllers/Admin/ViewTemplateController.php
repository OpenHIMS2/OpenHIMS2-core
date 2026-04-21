<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UnitTemplate;

class ViewTemplateController extends Controller
{
    public function index()
    {
        $unitTemplates = UnitTemplate::with('viewTemplates')->orderBy('name')->get();

        return view('admin.view-templates.index', compact('unitTemplates'));
    }
}
