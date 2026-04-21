<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\Unit;
use App\Models\UnitView;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with('institution')->orderBy('name')->get();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $institutions = Institution::orderBy('name')->get();

        return view('admin.users.create', compact('institutions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|min:8|confirmed',
            'role'           => 'required|in:admin,user',
            'institution_id' => 'nullable|exists:institutions,id',
        ]);

        $user = User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'password'       => $request->password,
            'role'           => $request->role,
            'institution_id' => $request->institution_id ?: null,
        ]);

        $user->units()->sync($request->input('unit_ids', []));
        $user->views()->sync($request->input('view_ids', []));

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $institutions = Institution::orderBy('name')->get();
        $userUnitIds  = $user->units->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $userViewIds  = $user->views->pluck('id')->map(fn($id) => (string) $id)->toArray();

        $units = $user->institution_id
            ? Unit::where('institution_id', $user->institution_id)->with('unitTemplate')->orderBy('name')->get()
            : collect();

        $views = $userUnitIds
            ? UnitView::whereIn('unit_id', $userUnitIds)->with('unit', 'viewTemplate')->get()
            : collect();

        return view('admin.users.edit', compact('user', 'institutions', 'units', 'views', 'userUnitIds', 'userViewIds'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => "required|email|unique:users,email,{$user->id}",
            'role'           => 'required|in:admin,user',
            'institution_id' => 'nullable|exists:institutions,id',
        ]);

        $data = [
            'name'           => $request->name,
            'email'          => $request->email,
            'role'           => $request->role,
            'institution_id' => $request->institution_id ?: null,
        ];

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $data['password'] = $request->password;
        }

        $user->update($data);
        $user->units()->sync($request->input('unit_ids', []));
        $user->views()->sync($request->input('view_ids', []));

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return back()->with('success', 'User deleted.');
    }

    public function unitsForInstitution(Institution $institution)
    {
        $units = Unit::where('institution_id', $institution->id)
            ->with('unitTemplate')
            ->orderBy('name')
            ->get()
            ->map(fn($u) => [
                'id'            => $u->id,
                'name'          => $u->name,
                'unit_template' => ['code' => $u->unitTemplate->code],
            ]);

        return response()->json($units);
    }

    public function viewsForUnits(Request $request)
    {
        $unitIds = $request->input('unit_ids', []);

        $views = UnitView::whereIn('unit_id', $unitIds)
            ->with('unit', 'viewTemplate')
            ->get()
            ->map(fn($v) => [
                'id'            => $v->id,
                'view_template' => ['name' => $v->viewTemplate->name],
                'unit'          => ['name' => $v->unit->name],
            ]);

        return response()->json($views);
    }
}
