# KFC Fast Food Ordering Kiosk (Laravel)

Guest kiosk ordering on **MySQL**, with admin store management, Leaflet delivery map, and TapUS-style **PayMongo** Hosted Checkout.

## Features

- Guest ordering (no customer account required)
- Modes: dine-in, take-out, delivery
- Delivery: pin location + choose admin-configured store (Leaflet + OSM)
- Session cart stored in MySQL
- Admin CRUD: menu, stores (name + map pin), users, order kanban
- PayMongo Hosted Checkout + webhook + admin reconcile
- Order tracking via token

## Tech Stack

- Laravel 13 (PHP 8.3+)
- MySQL
- Blade + Bootstrap 5
- Leaflet (maps)
- PayMongo Checkout Sessions v2

## Setup

1. Create MySQL database `kiosk_ordering`.

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

4. Migrate and seed:

```bash
php artisan migrate --seed
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

## PayMongo

Set in `.env`:

```
PAYMONGO_ENABLED=true
PAYMONGO_SECRET_KEY=sk_test_...
PAYMONGO_WEBHOOK_SECRET=whsec_...
```

Webhook URL: `POST /webhooks/paymongo` (event `checkout_session.payment.paid`).

Return URL is UX-only; payment is confirmed by webhook or **Admin → Orders → Refresh PayMongo**.

## Class / teaching branch

After the full app is on `main`, use branch `class/api-integration-starter` where Map + PayMongo integration is commented for live lessons.

## License

Educational / demo purposes.
