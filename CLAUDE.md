# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project overview

DANOSLA is a Laravel 12 transport management platform for **shippers**, **carriers**, and **admins**, built around a firm-bid negotiation workflow for shipments. Stack: Laravel 12, Livewire 4, Alpine.js, Tailwind CSS v4, Vite, Laravel Reverb (WebSockets) + Echo/Pusher-js client, Spatie permission/translatable, mcamara laravel-localization, Pest 4 for tests.

## Common commands

```bash
# Full dev environment (server + queue listener + pail logs + vite + reverb, all in one)
composer run dev

# Run the test suite (clears config cache first)
composer run test
# or
php artisan test

# Run a single test file / filter by name
php artisan test tests/Feature/ShippingLotFormTest.php
php artisan test --filter=test_name

# Frontend assets
npm run dev      # Vite dev server
npm run build    # production build

# Code style (Laravel Pint is a dev dependency; no custom pint.json, uses default Laravel preset)
vendor/bin/pint
```

Tests use Pest (`tests/Pest.php` binds `Tests\TestCase` for everything in `Feature`). `RefreshDatabase` is commented out by default in `Pest.php` — check whether a given test needs it before assuming DB state resets between tests.

## Architecture

### Domain: shipments and bids (the core feature)

The main business flow lives under `Shipment`/`ShipmentBid` and is exposed through a single controller: `app/Http/Controllers/Shipment/TransportFirmBidController.php` (routes prefixed `transport-firm-bid.*` in `routes/web.php`). Understanding this flow requires reading across several layers together:

- **Models**: `Shipment` hasMany `ShipmentBid` hasMany `BidMessage`; `Shipment` hasOne `Lot` and hasOne `Review`.
  - `Shipment::status` is a computed accessor: a `pending` shipment whose `validity_date` has passed is presented as `expired` (see `getIsExpiredAttribute`/`getValidityDiffAttribute`) without changing the stored column. `getCanNegotiateAttribute`/`getCanDemandAttribute` gate whether bidding is still allowed based on `validity_diff` (negotiation only within the last 180 minutes before `validity_date`).
  - A bid can be **negotiable** (full terms: price/dates) or a **direct request** (`is_negotiable = false`, terms left null) — `storeBid()` branches validation and persistence on this flag.
- **Authorization**: policies in `app/Policies/Shipment/` (`ShipmentPolicy`, `ShipmentBidPolicy`) are registered manually in `AppServiceProvider::boot()` via `Gate::policy(...)` rather than auto-discovered — if you add a new policy, register it there too. `ShipmentBidPolicy::negotiate()` encodes the same "urgent window" time logic as the model accessors; keep them in sync if the negotiation window rule changes.
- **Real-time**: bid actions broadcast on private channels authorized in `routes/channels.php` (`shipment.{shipmentId}`, `bid.{bidId}`) using `ShouldBroadcastNow` events in `app/Events/Shipment/` (`BidUpdated`, `NewBidMessage`). The frontend listens via `resources/js/echo.js` (Reverb broadcaster, driven by `VITE_REVERB_*` env vars). Broadcasting driver in `.env` is configured separately (`BROADCAST_CONNECTION`) from the queue.
- **Notifications**: `app/Notifications/Shipment/*` (`NewBidNotification`, `NewMessageNotification`, `ShipmentStatusChangedNotification`, `NewShipmentNotification`) each go out via `['database', 'broadcast']` channels — they populate both the persisted notifications table (bell icon UI, `app/Http/Controllers/Notification/NotificationController.php`) and push a live `BroadcastMessage`. `Shipment::toNotificationData()` is the shared payload shape reused across notification `toArray()` methods.
- Every state-changing action in the controller follows the same triple: mutate DB → `broadcast(...)->toOthers()` → `->notify(...)`. Follow this pattern when adding new bid/shipment actions instead of only doing one of the three.

### Localization

Uses `mcamara/laravel-localization` for URL-prefixed locales (`localize`, `localizationRedirect`, `localeSessionRedirect`, `localeViewPath` middleware aliases in `bootstrap/app.php`), but supported locales are **database-driven**, not just config: `AppServiceProvider::boot()` loads active `Language` rows (cached for 1h under `active_languages`) and overwrites `config('laravellocalization.supportedLocales')` at runtime, falling back to a hardcoded en/fr/ar set if the languages table is empty (important during fresh migrations). A separate DB-backed translation system exists under `app/Models/Translation.php` / `TranslationKey.php` managed via `dashboard.localization.*` routes (`routes/localization.php`).

### Access control & admin dashboard

Roles/permissions via `spatie/laravel-permission` (`User` uses `HasRoles`). Known roles referenced throughout the code: `admin`, `shipper`, `carrier`. Admin CRUD screens (users, roles, permissions, sectors, regions, countries, contacts, contact subjects) live under `app/Http/Controllers/Dashboard/**`, routed under `dashboard.*` names and gated by `auth` middleware — most follow a soft-delete + `restore`/`forceDelete` route convention (see `sectors`, `contact-subjects`, `regions`, `countries` in `routes/web.php`).

### Auth

Auth actions/pages are deliberately split: `app/Http/Controllers/Auth/Actions/*` (single-action invokable classes: `LoginAction`, `LogoutAction`, `RegisterAction`, `ForgotPasswordAction`, `ResetPasswordAction`) vs `app/Http/Controllers/Auth/Pages/*` (page-rendering controllers). Registration form itself is a Livewire component (`app/Livewire/Auth/Register.php`), not a plain controller form.

### Frontend

Blade views under `resources/views/pages/**` mirror the route groups (`transport-firm-bid`, `dashboard`, `notifications`, `auth`, `front`, `errors`). Livewire views live in `resources/views/livewire/**`. JS is organized per-feature under `resources/js/components/` (chart configs, calendar, map) plus `echo.js` for websockets and `bootstrap.js`/`app.js` as entry points bundled by Vite.
