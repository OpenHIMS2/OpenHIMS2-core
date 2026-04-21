<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TerminologyTerm;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TerminologyController extends Controller
{
    public function index()
    {
        $terms = TerminologyTerm::orderBy('term')
            ->get()
            ->groupBy('category');

        return view('admin.terminology.index', [
            'terms'      => $terms,
            'categories' => TerminologyTerm::$categories,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => ['required', 'string', Rule::in(array_keys(TerminologyTerm::$categories))],
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

        return back()->with('success', 'Term deleted.');
    }

    /**
     * JSON search endpoint — used by clinical autocomplete (auth middleware).
     * GET /terminology/search?category=presenting_complaints&q=cough
     */
    public function search(Request $request)
    {
        $request->validate([
            'category' => ['required', Rule::in(array_keys(TerminologyTerm::$categories))],
            'q'        => 'nullable|string|max:100',
        ]);

        $terms = TerminologyTerm::where('category', $request->category)
            ->when($request->q, fn ($q) => $q->where('term', 'like', '%' . $request->q . '%'))
            ->orderBy('term')
            ->pluck('term');

        return response()->json($terms);
    }
}
