<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TerminologyCategory;
use App\Models\TerminologyTerm;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TerminologyController extends Controller
{
    public function index()
    {
        $categories = TerminologyCategory::withCount('terms')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $terms = TerminologyTerm::orderBy('term')->get()->groupBy('category');

        return view('admin.terminology.index', compact('categories', 'terms'));
    }

    public function store(Request $request)
    {
        $validSlugs = TerminologyCategory::pluck('slug')->toArray();

        $request->validate([
            'category' => ['required', 'string', Rule::in($validSlugs)],
            'term'     => 'required|string|max:255',
        ]);

        $term = trim($request->term);

        $exists = TerminologyTerm::where('category', $request->category)
            ->where('term', $term)
            ->exists();

        if ($exists) {
            return back()->with('error', '"' . $term . '" already exists in this category.');
        }

        $created = TerminologyTerm::create([
            'category' => $request->category,
            'term'     => $term,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['id' => $created->id, 'term' => $created->term]);
        }

        return back();
    }

    public function destroy(TerminologyTerm $terminologyTerm)
    {
        $terminologyTerm->delete();

        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Term deleted.');
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'slug'        => ['required', 'string', 'max:50', 'regex:/^[a-z][a-z0-9_]*$/', 'unique:terminology_categories,slug'],
            'description' => 'nullable|string|max:255',
        ]);

        $maxOrder = TerminologyCategory::max('sort_order') ?? 0;

        TerminologyCategory::create([
            'name'        => $data['name'],
            'slug'        => $data['slug'],
            'description' => $data['description'] ?? null,
            'is_system'   => false,
            'sort_order'  => $maxOrder + 1,
        ]);

        return back()->with('success', 'Terminology box "' . $data['name'] . '" created.');
    }

    public function destroyCategory(TerminologyCategory $terminologyCategory)
    {
        if ($terminologyCategory->is_system) {
            return back()->with('error', 'System terminology boxes cannot be deleted.');
        }

        if ($terminologyCategory->terms()->exists()) {
            return back()->with('error', 'Cannot delete "' . $terminologyCategory->name . '" — delete all its terms first.');
        }

        $terminologyCategory->delete();

        return back()->with('success', 'Terminology box "' . $terminologyCategory->name . '" deleted.');
    }

    /**
     * JSON autocomplete — GET /terminology/search?category=slug&q=text
     */
    public function search(Request $request)
    {
        $validSlugs = TerminologyCategory::pluck('slug')->toArray();

        $request->validate([
            'category' => ['required', Rule::in($validSlugs)],
            'q'        => 'nullable|string|max:100',
        ]);

        $terms = TerminologyTerm::where('category', $request->category)
            ->when($request->q, fn ($q) => $q->where('term', 'like', '%' . $request->q . '%'))
            ->orderBy('term')
            ->pluck('term');

        return response()->json($terms);
    }
}
