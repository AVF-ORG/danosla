<?php

namespace App\Http\Controllers\Dashboard\Localization;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Translation;
use App\Models\TranslationKey;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();

        $translations = Translation::query()
            ->with(['key', 'language'])
            ->when($q, fn($query) => $query->whereHas('key', fn($k) => $k->where('key', 'like', "%{$q}%")))
            ->orderBy(TranslationKey::select('group')->whereColumn('translation_keys.id', 'translations.translation_key_id'))
            ->paginate(30)
            ->withQueryString();

        return view('pages.dashboard.localization.translations.index', compact('translations', 'q'));
    }


    public function edit(TranslationKey $translationKey)
    {
        $languages = Language::query()
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $existing = Translation::query()
            ->where('translation_key_id', $translationKey->id)
            ->get()
            ->keyBy('language_id');

        return view('pages.dashboard.localization.translations.edit', compact('translationKey', 'languages', 'existing'));
    }

    public function update(Request $request, TranslationKey $translationKey)
    {
        $data = $request->validate([
            'values' => ['required', 'array'],
            'values.*' => ['nullable', 'string'],
        ]);

        // Only allow existing language IDs
        $languageIds = array_keys($data['values']);
        $languages = Language::query()->whereIn('id', $languageIds)->get()->keyBy('id');

        foreach ($data['values'] as $languageId => $value) {
            if (!isset($languages[$languageId])) {
                continue;
            }

            Translation::updateOrCreate(
                ['translation_key_id' => $translationKey->id, 'language_id' => $languageId],
                ['value' => $value]
            );
        }

        return redirect()
            ->route('pages.dashboard.localization.translations.edit', $translationKey)
            ->with('success', 'Translations updated.');
    }
}
