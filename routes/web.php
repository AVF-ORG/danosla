<?php

use App\Http\Controllers\Auth\Actions\LogoutAction;
use App\Http\Controllers\Dashboard\Country\CountryController;
use App\Http\Controllers\Dashboard\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\Permission\PermissionController;
use App\Http\Controllers\Dashboard\Region\RegionController;
use App\Http\Controllers\Dashboard\RegionCountry\RegionCountryController;
use App\Http\Controllers\Dashboard\Role\RoleController;
use App\Http\Controllers\Dashboard\Sector\SectorController;
use App\Http\Controllers\Dashboard\ContactSubject\ContactSubjectController;
use App\Http\Controllers\Dashboard\Contact\ContactController;
use App\Http\Controllers\Dashboard\User\UserController;
use App\Http\Controllers\TransportFirmBidController;
use App\Http\Controllers\User\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
Route::get('/empty2', function () {
    return view('pages.empty2', ['title' => 'Empty page']);
})->name('empty2');

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => [
        'localize',
        'localizationRedirect',
        'localeSessionRedirect',
        'localeViewPath',
    ],
], function () {

    Route::middleware('auth')->group(function () {
        Route::post('/logout', LogoutAction::class)->name('logout');

        Route::prefix('dashboard')
            ->name('dashboard.')
            ->group(function () {
                Route::get('/', DashboardController::class)->name('index');
            });

        Route::prefix('profile')
            ->name('profile.')
            ->group(function () {
                Route::get('/{tab?}', [ProfileController::class, 'index'])->name('index');
                Route::put('/{user}', [ProfileController::class, 'update'])->name('update');
            });

        // Route::get('/empty', function () {
        //     return view('pages.empty', ['title' => 'Empty page']);
        // })->name('empty');


        Route::prefix('dashboard')->name('dashboard.')->group(function () {
            // Sectors
            Route::resource('sectors', SectorController::class);

            Route::get('sectors/{id}/restore',
                [SectorController::class, 'restore']
            )->name('sectors.restore');

            Route::delete('sectors/{id}/force-delete',
                [SectorController::class, 'forceDelete']
            )->name('sectors.forceDelete');

            Route::get('sectors/{sector}/json', [SectorController::class, 'json'])
                ->name('sectors.json');

            // Contact Subjects
            Route::resource('contact-subjects', ContactSubjectController::class);

            Route::get('contact-subjects/{id}/restore',
                [ContactSubjectController::class, 'restore']
            )->name('contact-subjects.restore');

            Route::delete('contact-subjects/{id}/force-delete',
                [ContactSubjectController::class, 'forceDelete']
            )->name('contact-subjects.forceDelete');

            Route::get('contact-subjects/{contactSubject}/json', [ContactSubjectController::class, 'json'])
                ->name('contact-subjects.json');

            // Contacts
            Route::resource('contacts', ContactController::class)->only(['index', 'show', 'destroy']);
            Route::post('contacts/{contact}/reply', [ContactController::class, 'updateReply'])->name('contacts.reply');

            // Regions
            Route::resource('regions', RegionController::class);

            Route::get('regions/{id}/restore',
                [RegionController::class, 'restore']
            )->name('regions.restore');

            // Countries
            Route::resource('countries', CountryController::class);

            Route::get('countries/{id}/restore',
                [CountryController::class, 'restore']
            )->name('countries.restore');

            // Region-Country Relationships
            Route::prefix('region-countries')->name('region-countries.')->group(function () {
                Route::get('/', [RegionCountryController::class, 'index'])->name('index');
                Route::get('/create', [RegionCountryController::class, 'create'])->name('create');
                Route::post('/', [RegionCountryController::class, 'store'])->name('store');
                Route::delete('/{regionId}/{countryId}', [RegionCountryController::class, 'destroy'])->name('destroy');
            });

            // Roles & Permissions
            Route::resource('roles', RoleController::class);
            Route::resource('permissions', PermissionController::class);

            // User Assignment & Management
            Route::get('users/pending', [UserController::class, 'pending'])->name('users.pending');
            Route::patch('users/{user}/status', [UserController::class, 'updateStatus'])->name('users.update-status');
            
            // Clean Role Routes
            Route::get('users/admins', [UserController::class, 'index'])->defaults('role', 'admin')->name('users.admins');
            Route::get('users/transporters', [UserController::class, 'index'])->defaults('role', 'transporter')->name('users.transporters');
            Route::get('users/customer-transporters', [UserController::class, 'index'])->defaults('role', 'customer-transporter')->name('users.customers');

            Route::resource('users', UserController::class);
        });

    });

    Route::post('/locale', function (Request $request) {
        $locale = $request->input('locale', config('app.locale'));

        // Optional: validate allowed locales
        abort_unless(in_array($locale, ['en', 'fr', 'ar']), 400);

        session(['locale' => $locale]);

        return back();
    })->name('locale.set');

    // dashboard pages
    Route::get('/', function () {
        return view('pages.front.landing', ['title' => 'Home Page']);
    })->name('landing');

    // transport firm bid page
    Route::prefix('transport-firm-bid')->name('transport-firm-bid.')->group(function () {
        Route::get('/', [TransportFirmBidController::class, 'index'])->name('index');
        Route::get('/create', [TransportFirmBidController::class, 'create'])->name('create');
        Route::get('/{shipment}', [TransportFirmBidController::class, 'show'])->name('show');
        Route::get('/{shipment}/edit', [TransportFirmBidController::class, 'edit'])->name('edit');
        Route::delete('/{shipment}', [TransportFirmBidController::class, 'destroy'])->name('destroy');
    });

    // calender pages
    Route::get('/calendar', function () {
        return view('pages.calender', ['title' => 'Calendar']);
    })->name('calendar');

    // error pages
    Route::get('/error-403', function () {
        return view('pages.errors.error-403', ['title' => 'Error 403']);
    })->name('error-403');

    Route::get('/error-404', function () {
        return view('pages.errors.error-404', ['title' => 'Error 404']);
    })->name('error-404');

    Route::get('/error-419', function () {
        return view('pages.errors.error-419', ['title' => 'Error 419']);
    })->name('error-419');

    Route::get('/error-429', function () {
        return view('pages.errors.error-429', ['title' => 'Error 429']);
    })->name('error-429');

    Route::get('/error-500', function () {
        return view('pages.errors.error-500', ['title' => 'Error 500']);
    })->name('error-500');

    Route::get('/error-503', function () {
        return view('pages.errors.error-503', ['title' => 'Error 503']);
    })->name('error-503');

});
