# CareLink Development Status

_Last updated: 2026-11-26_

## Phase 1 — Architecture & Foundation: COMPLETE
- Laravel 13 scaffolded, core packages installed (spatie/laravel-permission, spatie/laravel-activitylog, laravel/reverb, meilisearch, pest)
- Tailwind CSS v4 + Alpine.js + Vite configured and building (`npm run build` passes)
- 15 migrations for all core entities; 20 Eloquent models with relationships
- 4 seeders (Organisation, User, PricingRule, Vehicle) — 2 orgs, 6 users, 12 pricing rules, 5 vehicles
- Migrations + seeders run successfully on SQLite (dev)

## Phase 2 — Organizations & Identity: PARTIAL
- Organisation, User, Patient, PatientContact models + migrations done
- Login / Register / Forgot / Reset / Verify / Confirm-password views + routes scaffolded
- Auth routes wired (login POST/logout functional; register/reset are stubs pending Fortify/custom controllers)
- RBAC roles/permissions via spatie/laravel-permission (seeded)

## Phase 3 — Patient Website & Booking Engine: MOSTLY COMPLETE
- Public pages: home, about, how-it-works, services, faq, contact, privacy, cookie, terms, accessibility (all 200)
- 6-step booking wizard (session-backed) with validation, fare estimate, idempotency key, reCAPTCHA hook
- Booking submission creates Patient + PatientContact + Booking + status history in a DB transaction
- Confirmation page + public status lookup with Reverb live-update wiring (polling fallback)
- Cancel / reschedule flows with 2-hour rule
- Patient BookingController: `showStep` fixed (stepTitles scope bug resolved)

## Phase 4 — Operations Portal: PARTIAL
- Operations layout + dashboard with KPIs, revenue/status charts (Chart.js), recent + upcoming bookings
- Bookings index (filters, stats, pagination) + show (assignment modals, status history, cancel)
- Accept / reject / assign-vehicle / assign-driver / dispatch / cancel controller actions
- Run-sheet view (printable)
- Stubs created: dispatch board, fleet (CRUD views), drivers (CRUD views), compliance, reports, commissions, settlements, partners, settings

## Phase 5 — Partner Portal: STUBS ONLY
- Partner controllers + minimal views (dashboard, bookings, settlements, fleet, drivers)

## Known Issues / Blockers
- MySQL + Docker not installed locally — using SQLite for dev; production needs Docker/K8s
- bcmath + redis PHP extensions missing on Windows (available in Docker)
- Horizon needs pcntl/posix (Unix-only) — using database queue driver in dev
- Pest Laravel plugin incompatible with Laravel 13 — using PHPUnit
- reCAPTCHA verification is a no-op in local/testing (TODO: real Google verify)
- Partner/Operations resource controllers (edit/update/destroy) are stubs
- Auth register/reset POST handlers are stubs (need Fortify or custom controllers)

## Next Steps
1. Implement real auth controllers (register/login/reset) or install Fortify
2. Build dispatch board live assignment + Reverb broadcasts
3. Fleet/Driver document upload + compliance expiry tracking (COFC, PrDP, Form MC)
4. FareCalculatorService with real distance calc + pricing rules
5. Commission + settlement generation/approval workflow
6. Notifications (SMS/email) on booking events
7. Test suite (PHPUnit) for booking state machine + fare + POPIA consent