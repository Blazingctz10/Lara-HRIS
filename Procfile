web: touch database/database.sqlite && php artisan migrate --force && (php artisan db:seed --force || true) && php artisan storage:link --force && php artisan serve --host=0.0.0.0 --port=$PORT
