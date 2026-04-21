<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use Illuminate\Http\Request;

class HierarchyController extends Controller
{
    public function index()
    {
        $institutions = Institution::whereNull('parent_id')
            ->with('allChildren')
            ->orderBy('name')
            ->get();

        return view('admin.hierarchy.index', compact('institutions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'code'    => ['required', 'regex:/^\d{1,3}$/'],
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'logo'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $parentId = $request->parent_id ?: null;
        $code     = str_pad((int) $request->code, 3, '0', STR_PAD_LEFT);

        if ($this->siblingCodeExists($code, $parentId)) {
            return back()
                ->withInput()
                ->withErrors(['code' => 'This code is already used by another institution at the same level.']);
        }

        $logoFilename = null;
        if ($request->hasFile('logo')) {
            $logoFilename = $this->uploadLogo($request->file('logo'));
        }

        Institution::create([
            'name'      => $request->name,
            'parent_id' => $parentId,
            'code'      => $code,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'address'   => $request->address,
            'logo'      => $logoFilename,
        ]);

        return back()->with('success', 'Institution created successfully.');
    }

    public function update(Request $request, Institution $hierarchy)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'code'    => ['required', 'regex:/^\d{1,3}$/'],
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'logo'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $code = str_pad((int) $request->code, 3, '0', STR_PAD_LEFT);

        if ($this->siblingCodeExists($code, $hierarchy->parent_id, $hierarchy->id)) {
            return back()
                ->withInput()
                ->withErrors(['code' => 'This code is already used by another institution at the same level.']);
        }

        $data = [
            'name'    => $request->name,
            'code'    => $code,
            'email'   => $request->email,
            'phone'   => $request->phone,
            'address' => $request->address,
        ];

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($hierarchy->logo) {
                $old = public_path('institution_logos/' . $hierarchy->logo);
                if (file_exists($old)) {
                    unlink($old);
                }
            }
            $data['logo'] = $this->uploadLogo($request->file('logo'));
        }

        $hierarchy->update($data);

        return back()->with('success', 'Institution updated successfully.');
    }

    public function destroy(Institution $hierarchy)
    {
        if ($hierarchy->logo) {
            $file = public_path('institution_logos/' . $hierarchy->logo);
            if (file_exists($file)) {
                unlink($file);
            }
        }

        $hierarchy->delete();

        return back()->with('success', 'Institution deleted.');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function siblingCodeExists(string $code, ?int $parentId, ?int $excludeId = null): bool
    {
        $query = Institution::where('code', $code);

        if ($parentId) {
            $query->where('parent_id', $parentId);
        } else {
            $query->whereNull('parent_id');
        }

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    private function uploadLogo($file): string
    {
        $dir = public_path('institution_logos');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $filename = 'inst_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $filename);
        return $filename;
    }
}
