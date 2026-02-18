<?php

namespace App\Http\Controllers\Dashboard\Localization;

use App\Http\Controllers\Controller;
use App\Models\TranslationKey;
use Illuminate\Http\Request;

class KeyController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $group = $request->string('group')->toString();

        $keys = TranslationKey::query()
            ->when($q, fn($query) => $query->where('key', 'like', "%{$q}%"))
            ->when($group, fn($query) => $query->where('group', $group))
            ->orderBy('group')
            ->orderBy('key')
            ->paginate(30)
            ->withQueryString();

        $groups = TranslationKey::query()
            ->select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group');

        return view('pages.dashboard.localization.keys.index', compact('keys', 'groups', 'q', 'group'));
    }

    public function create()
    {
        return view('pages.dashboard.localization.keys.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'key' => ['required', 'string', 'max:255', 'unique:translation_keys,key'],
            'group' => ['required', 'string', 'max:100'],
        ]);

        TranslationKey::create($data);

        return redirect()
            ->route('pages.dashboard.localization.keys.index')
            ->with('success', 'Key created.');
    }

    public function edit(TranslationKey $translationKey)
    {
        return view('pages.dashboard.localization.keys.edit', compact('translationKey'));
    }

    public function update(Request $request, TranslationKey $translationKey)
    {
        $data = $request->validate([
            'key' => ['required', 'string', 'max:255', 'unique:translation_keys,key,' . $translationKey->id],
            'group' => ['required', 'string', 'max:100'],
        ]);

        $translationKey->update($data);

        return redirect()
            ->route('pages.dashboard.localization.keys.index')
            ->with('success', 'Key updated.');
    }

    public function destroy(TranslationKey $translationKey)
    {
        $translationKey->delete();

        return redirect()
            ->route('pages.dashboard.localization.keys.index')
            ->with('success', 'Key deleted.');
    }
}
