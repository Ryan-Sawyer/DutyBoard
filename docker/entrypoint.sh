#!/bin/sh
# Runs before the app or worker starts in the development containers.
set -e

# First run: install PHP packages into ./vendor.
if [ ! -f vendor/autoload.php ]; then
    echo "Installing PHP packages (first run only)..."
    composer install --no-interaction --no-progress
fi

# The app can't run without an encryption key, kept in .env on your machine.
if ! grep -qE '^APP_KEY=.+' .env 2>/dev/null; then
    echo "APP_KEY is empty. Run this, then paste the result into APP_KEY in .env:"
    echo "  docker compose run --rm app php artisan key:generate --show"
    exit 1
fi

# Wait for PostgreSQL to accept connections.
until php artisan db:show > /dev/null 2>&1; do
    echo "Waiting for the database..."
    sleep 2
done

# Only the app container applies database changes, so two containers
# never try to migrate at once.
if [ "$RUN_MIGRATIONS" = "true" ]; then
    php artisan migrate --force
fi

exec "$@"
