# laravel10-user-module-api-with-solid-principle
# laravel scribe features added
# solid funtionality

## Hotel room reservation (assessment)

Single-page UI at `/` with session-backed room state.

- **Book:** Enter 1–5 rooms; the server picks an available set that prefers one full floor (minimum corridor span), otherwise minimizes worst-case travel time between pairs (lift-left metric: horizontal + 2 min per floor change).
- **Random:** Randomly marks many rooms as occupied (unavailable).
- **Reset:** Clears all occupied and booking flags.

### Run locally

```bash
composer install
cp .env.example .env   # if needed
php artisan key:generate
npm install && npm run build
php artisan serve
```

Open `http://127.0.0.1:8000`. For a public “live URL”, deploy as usual (e.g. Laravel Forge, Railway, VPS) after `npm run build` and `php artisan config:cache`.
