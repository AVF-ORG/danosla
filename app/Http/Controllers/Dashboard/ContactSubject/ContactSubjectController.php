<?php

namespace App\Http\Controllers\Dashboard\ContactSubject;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\ContactSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContactSubjectController extends Controller
{
    /**
     * Display a listing of contact subjects.
     */
    public function index(Request $request)
    {
        $query = ContactSubject::query();

        // Trash filter
        if ($request->get('trash') == 1) {
            $query->onlyTrashed();
        }

        $contactSubjects = $query->latest()->paginate(15);

        return view('pages.dashboard.contact-subjects.index', compact('contactSubjects'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $languages = Language::where('is_active', true)->get();

        return view('pages.dashboard.contact-subjects.create', compact('languages'));
    }

    /**
     * Store contact subject.
     */
    public function store(Request $request)
    {
        $activeCodes = Language::where('is_active', true)->pluck('code')->toArray();

        $request->validate([
            'name' => ['required', 'array'],
            'name.*' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Keep only active languages and remove empty values
        $names = collect($request->input('name', []))
            ->only($activeCodes)
            ->filter(fn ($value) => filled($value))
            ->toArray();

        if (empty($names)) {
            return back()->withErrors(['name' => 'Please enter at least one translation.'])->withInput();
        }

        // Generate slugs
        $slugs = [];
        foreach ($names as $code => $value) {
            $slugs[$code] = Str::slug($value);
        }

        ContactSubject::create([
            'name' => $names,
            'slug' => $slugs,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('dashboard.contact-subjects.index')
            ->with('success', 'Contact Subject created successfully.');
    }

    /**
     * Show contact subject details.
     */
    public function show($id)
    {
        $contactSubject = ContactSubject::findOrFail($id);

        return view('pages.dashboard.contact-subjects.show', compact('contactSubject'));
    }

    /**
     * Show edit form.
     */
    public function edit($id)
    {
        $contactSubject = ContactSubject::findOrFail($id);
        $languages = Language::where('is_active', true)->get();

        return view('pages.dashboard.contact-subjects.edit', compact('contactSubject', 'languages'));
    }

    /**
     * Update contact subject.
     */
    public function update(Request $request, $id)
    {
        $contactSubject = ContactSubject::findOrFail($id);
        $activeCodes = Language::where('is_active', true)->pluck('code')->toArray();

        $request->validate([
            'name' => ['required', 'array'],
            'name.*' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $names = collect($request->input('name', []))
            ->only($activeCodes)
            ->filter(fn ($value) => filled($value))
            ->toArray();

        if (empty($names)) {
            return back()->withErrors(['name' => 'Please enter at least one translation.'])->withInput();
        }

        $slugs = [];
        foreach ($names as $code => $value) {
            $slugs[$code] = Str::slug($value);
        }

        $contactSubject->update([
            'name' => $names,
            'slug' => $slugs,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('dashboard.contact-subjects.index')
            ->with('success', 'Contact Subject updated successfully.');
    }

    /**
     * Soft delete contact subject.
     */
    public function destroy($id)
    {
        $contactSubject = ContactSubject::findOrFail($id);
        $contactSubject->delete();

        return redirect()
            ->route('dashboard.contact-subjects.index')
            ->with('success', 'Contact Subject deleted successfully.');
    }

    /**
     * Restore soft deleted contact subject.
     */
    public function restore($id)
    {
        ContactSubject::withTrashed()->findOrFail($id)->restore();

        return redirect()
            ->route('dashboard.contact-subjects.index')
            ->with('success', 'Contact Subject restored successfully.');
    }

    /**
     * Permanently delete contact subject.
     */
    public function forceDelete($id)
    {
        ContactSubject::withTrashed()->findOrFail($id)->forceDelete();

        return redirect()
            ->route('dashboard.contact-subjects.index')
            ->with('success', 'Contact Subject permanently deleted.');
    }

    public function json(ContactSubject $contactSubject)
    {
        return response()->json([
            'id' => $contactSubject->id,
            'is_active' => (bool) $contactSubject->is_active,
            'name' => $contactSubject->getTranslations('name') ?? [],
        ]);
    }
}
