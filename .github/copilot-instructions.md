## Giscana — quick onboarding for AI code assistants

This file captures the concrete, repository-specific knowledge an AI agent needs to be productive in this Laravel-based GIS project.

- Stack: Laravel 12 (PHP ^8.2), Blade + Tailwind, Vite, MySQL (project README), Leaflet/OpenStreetMap for mapping.
- Key locations: `app/Models/` (spatial models), `app/Http/Controllers/` (API + map controllers), `resources/views/map/`, `resources/js/app.js`, `resources/css/app.css`, `database/migrations/` (spatial schema), `routes/web.php` and `routes/api.php`.

Primary goals for assistants
- Preserve spatial data shape: many migrations store geometry as JSON (e.g. `disaster_zones.polygon_coordinates`), so keep GeoJSON-compatible shapes when updating APIs, seeders or models.
- Respect existing auth scaffold: routes use Laravel auth (Breeze). Many pages reference `Auth::user()`; don't remove or replace without checking `routes/auth.php` and `app/Providers`.

Developer workflows and exact commands (copyable)
- Full setup (installs deps, migrates, builds assets):
  - `composer run setup` (runs composer scripts that install php deps, copy .env, generate key, migrate and build assets)
  - If needed: `npm install` then `npm run build`
- Run development environment (concurrent services):
  - `composer run dev` — runs `php artisan serve`, queue worker, pail logger, and `npm run dev` concurrently (recommended for local dev)
  - Alternatively run separately:
    - `php artisan serve`
    - `npm run dev`
- Tests: `composer test` (clears config and runs `php artisan test`). For a single suite use `php artisan test --filter NameOfTest`.

Project-specific conventions and patterns
- Spatial storage: migrations often use `json` columns to store GeoJSON coordinates (see `database/migrations/2025_10_23_042037_create_disaster_zones_table.php`). Keep REST APIs that accept geometry expecting GeoJSON-like payloads (arrays of coordinates). Models in `app/Models/` wrap these fields.
- Blade + Tailwind: Frontend is Blade templates with Tailwind. Source CSS/JS are in `resources/css/app.css` and `resources/js/app.js` and wired to Vite in `vite.config.js`.
- Map endpoints: map pages and data endpoints are in `routes/web.php` (e.g. `map.index`, `map.data`, `map.search`) and implemented by `MapController`. When changing map data format, update both controller output and the JS that consumes `/map/data`.
- Auth & roles: The app has multiple roles (BPBD Administrators, Staff, Public). Permission checks appear in controllers and policies—verify `app/Http/Controllers` for role-based logic before altering endpoints.

Integration points & external deps to be mindful of
- Vite + laravel-vite-plugin — affects how assets are built and served in dev vs prod (`npm run dev` vs `npm run build`). Use `resources/js/app.js` entry.
- Packages referenced in `composer.json` (e.g. `laravel/pail`, `laravel/breeze`) are used by the dev stack; the `composer run dev` script depends on `php artisan pail` (logs/watch). Keep these scripts intact unless you fully test replacement flows.
- Database: README recommends MySQL but composer post-create has convenience touches for sqlite — review `.env` and `config/database.php` when changing DB logic.

When editing code, test-safety checklist
1. Update/seed data shape: When changing model columns for spatial data, add a migration and update `database/seeders/GiscanaDataSeeder.php` (if present) and relevant factories.
2. Frontend contract: If changing a JSON response consumed by the map, update `resources/js/app.js` and `resources/views/map/*` accordingly.
3. Run migrations and seeders locally: `php artisan migrate` and `php artisan db:seed --class=GiscanaDataSeeder` (or run the composer `setup` script in a safe dev DB).
4. Run tests: `composer test` and manually exercise the `/map` UI in the browser (or via `npm run dev` + `php artisan serve`).

Files you will cite when making changes (examples)
- Routes and map endpoints: `routes/web.php`
- Spatial schema example: `database/migrations/2025_10_23_042037_create_disaster_zones_table.php`
- Models: `app/Models/DisasterZone.php`, `app/Models/EvacuationRoute.php`, `app/Models/EvacuationFacility.php`, `app/Models/AidDistributionPoint.php`
- Map UI and templates: `resources/views/map/`, `resources/views/dashboard.blade.php`
- Asset entry points: `resources/js/app.js`, `resources/css/app.css`, `vite.config.js`
- Dev orchestration scripts: `composer.json` (scripts: `setup`, `dev`, `test`)

How to ask for further clarifications
- If you need the exact GeoJSON schema used for a model, point me to the model file in `app/Models/` and I will extract the serialization/attribute casts.
- If a JS issue is map-related, include the API response sample (JSON) and the consuming snippet in `resources/js/app.js`.

If this looks good, I will commit this file. If you'd like more detail (examples of JSON payloads, common test failures, or a short quickstart snippet tailored to Windows + Laragon), tell me which part to expand.
