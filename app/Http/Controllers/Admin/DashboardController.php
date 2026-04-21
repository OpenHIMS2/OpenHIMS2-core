<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\Unit;
use App\Models\UnitView;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'institutionCount' => Institution::count(),
            'unitCount'        => Unit::count(),
            'userCount'        => User::where('role', 'user')->count(),
            'viewCount'        => UnitView::count(),
        ]);
    }
}
