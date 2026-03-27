# KFC Fast Food Ordering Kiosk (Laravel)

A simple kiosk-style ordering web app built with Laravel and Blade.

Users can:
- Choose ordering mode (`dine-in` or `take-out`)
- Browse menu items
- Add items with variation/add-ons
- Manage cart quantities directly in cart
- Remove items from cart
- Checkout and place orders

This project currently uses JSON files as dummy storage (no database required for ordering data).

## Features

- Kiosk-inspired UI with fixed bottom navigation (menu/cart flow)
- Mode-aware flow (`dine-in` and `take-out`) preserved across retries
- Cart quantity controls (`+`, `-`, direct input auto-update)
- Remove-from-cart action
- Order success/failure simulation
- Local JSON persistence for:
	- menu items
	- cart
	- order history

## Tech Stack

- Laravel (PHP)
- Blade templates
- Bootstrap 5
- Local JSON files for app data

## Project Structure (key parts)

- `app/Http/Controllers/OrderingController.php` — core ordering logic
- `routes/web.php` — ordering routes
- `resources/views/ordering/` — all kiosk pages
- `dummy data/menu.json` — menu source
- `dummy data/cart.json` — active cart storage
- `dummy data/orders.json` — placed orders

## Ordering Routes

- `GET /ordering` — mode selection
- `GET /menu?mode=dine-in|take-out` — menu page
- `GET /cart?mode=dine-in|take-out` — cart page
- `POST /cart/update-quantity` — update/remove cart item
- `GET /checkout?mode=dine-in|take-out` — checkout page
- `POST /add-to-cart` — add item to cart
- `POST /place-order` — place order
- `GET /success` — success page
- `GET /failure` — failure page

## Setup

1. Install dependencies:

```bash
composer install
npm install
```

2. Create environment file:

```bash
cp .env.example .env
php artisan key:generate
```

3. Ensure these files/folders exist:

- `dummy data/menu.json`
- `dummy data/cart.json`
- `dummy data/orders.json`

4. Run the app:

```bash
php artisan serve
```

Open: `http://127.0.0.1:8000/ordering`

## Notes

- Checkout currently simulates payment success/failure randomly for demo purposes.
- Cart is only cleared on successful order placement.
- If checkout fails, users can return to cart and retry with mode preserved.

## Useful Commands

```bash
php artisan view:clear
php artisan cache:clear
```

## License

This project is for educational/demo purposes.
