<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Language;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $languages = Cache::remember('active_languages', 3600, function () {
            return Language::query()
                ->where('is_active', 1)
                ->orderBy('name')
                ->get(['code', 'name']);
        });

        View::share('languages', $languages);

        $allLocales = config('laravellocalization.supportedLocales', []);

        config()->set('laravellocalization.supportedLocales',
            $languages->mapWithKeys(fn ($l) => [
                $l->code => array_merge(
                    $allLocales[$l->code] ?? [
                        'script' => str_starts_with($l->code, 'ar') ? 'Arab' : 'Latn',
                        'native' => $l->name,
                        'regional' => $l->code,
                    ],
                    ['name' => $l->name]
                ),
            ])->toArray()
        );
    
    }
}
