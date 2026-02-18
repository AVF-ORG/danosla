<?php

namespace App\Http\Controllers\Dashboard\Localization;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function index()
    {
        $languages = Language::query()
            ->orderByDesc('is_active')
            ->orderBy('code')
            ->paginate(20);

        return view('pages.dashboard.localization.languages.index', compact('languages'));
    }

    public function create()
    {
        return view('pages.dashboard.localization.languages.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:10', 'unique:languages,code'],
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        Language::create($data);

        return redirect()
            ->route('pages.dashboard.localization.languages.index')
            ->with('success', 'Language created.');
    }

    public function edit(Language $language)
    {
        return view('pages.dashboard.localization.languages.edit', compact('language'));
    }

    public function update(Request $request, Language $language)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:10', 'unique:languages,code,' . $language->id],
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        $language->update($data);

        return redirect()
            ->route('pages.dashboard.localization.languages.index')
            ->with('success', 'Language updated.');
    }

    public function destroy(Language $language)
    {
        $language->delete();

        return redirect()
            ->route('pages.dashboard.localization.languages.index')
            ->with('success', 'Language deleted.');
    }
}

