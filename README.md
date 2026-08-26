# KFC Fast Food Ordering Kiosk (Laravel)

Guest kiosk ordering on **MySQL**, with admin store management, Leaflet delivery map, and TapUS-style **PayMongo** Hosted Checkout.

## Features

- Guest ordering (no customer account required)
- Modes: dine-in, take-out, delivery
- Delivery: pin location + choose admin-configured store (Leaflet + OSM)
- Session cart stored in MySQL
- Admin CRUD: menu (with image upload), stores (name + map pin), users, order kanban
- PayMongo Hosted Checkout + webhook + admin reconcile
- Order tracking via token

## Tech Stack

- Laravel 13 (PHP 8.3+)
- MySQL
- Blade + Bootstrap 5
- Leaflet (maps)
- PayMongo Checkout Sessions v2 (create) / v1 (retrieve for reconcile)

## Setup

1. Create **one** MySQL database named `kiosk_ordering` (phpMyAdmin or CLI).  
   Do not create a second database such as `kiosk_ordering_system` — Laravel only uses `DB_DATABASE` from `.env`.

```sql
CREATE DATABASE kiosk_ordering CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Install dependencies:

```bash
composer install
npm install
```

3. Environment:

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` for MySQL (`DB_*`) and optional PayMongo keys.

4. Migrate, seed, and link public storage (required for menu image uploads):

```bash
php artisan migrate --seed
php artisan storage:link
```

Seeded admin:

- Email: `admin@kiosk.test`
- Password: `password`

Seeded stores include map coordinates (Megamall, MOA, Trinoma) so delivery demos work immediately.

5. Run:

```bash
php artisan serve
```

Open: `http://127.0.0.1:8000/ordering`

Optional Vite: `npm run dev` or full stack `composer run dev`.

## PayMongo (classroom demo — no webhook required)

Set in `.env`:

```
PAYMONGO_ENABLED=true
PAYMONGO_SECRET_KEY=sk_test_...
PAYMONGO_WEBHOOK_SECRET=
PAYMONGO_API_BASE=https://api.paymongo.com
PAYMONGO_PAYMENT_METHOD_TYPES=card,gcash,paymaya,qrph
```

Then:

1. Guest checkout with card / wallet (not cash) → PayMongo hosted page  
2. Complete test payment  
3. Return URL is UX-only (does not mark paid)  
4. Admin → Orders → **Refresh PayMongo** to mark the order `paid`  

Optional later: webhook at `POST /webhooks/paymongo` for `checkout_session.payment.paid`.

## License

Educational / demo purposes.
