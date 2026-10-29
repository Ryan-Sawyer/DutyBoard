#!/bin/sh
# Runs before the app or worker starts in the development containers.
set -e

# First run: install PHP packages into ./vendor.
if [ ! -f vendor/autoload.php ]; then
    echo "Installing PHP packages (first run only)..."
    composer install --no-interaction --no-progress
fi

# The app can't run without an encryption key. It's stored in .env, so it
# has to be generated on your machine, not inside the container.
if [ -z "$APP_KEY" ]; then
    echo "APP_KEY is empty. Run this, then paste the result into APP_KEY in .env:"
    echo "  docker compose run --rm app php artisan key:generate --show"
    exit 1
fi

# Wait for PostgreSQL to accept connections.
until php -r 'new PDO("pgsql:host=".getenv("DB_HOST").";port=".getenv("DB_PORT").";dbname=".getenv("DB_DATABASE"), getenv("DB_USERNAME"), getenv("DB_PASSWORD"));' 2>/dev/null; do
    echo "Waiting for the database..."
    sleep 2
done

# Only the app container applies database changes, so two containers
# never try to migrate at once.
if [ "$RUN_MIGRATIONS" = "true" ]; then
    php artisan migrate --force
fi

exec "$@"
