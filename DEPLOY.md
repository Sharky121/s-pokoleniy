# Перенос сайта на сервер (без Docker)

## 1. Что копировать / деплой через Git

- Размещение через **git**: склонируйте репозиторий на сервер и делайте `git pull` при обновлении. Загруженные картинки (новости, галереи и т.д.) лежат в `storage/app/public/vendor/cherry-site/upload/` и **уже добавлены в репозиторий** — при `git pull` они подтянутся на сервер.
- **Один раз** закоммитьте папку `upload`, если ещё не коммитили:
  ```bash
  git add storage/app/public/vendor/cherry-site/upload/
  git add storage/app/public/.gitignore
  git commit -m "Добавить загруженные файлы админки в репозиторий"
  git push
  ```
- Альтернатива без Git: копируйте проект целиком и **обязательно** папку `storage/app/public/vendor/cherry-site/upload/`.
- **Можно не копировать** (на сервере заново поставить):
  - `vendor/` — на сервере выполнить `composer install --no-dev`
  - `node_modules/` — если не собираете фронт на сервере, можно не копировать
- Файл **`.env`** на сервере создайте вручную или скопируйте и поправьте под прод (см. ниже).

## 2. На сервере после копирования

```bash
cd /путь/к/s-pokoleniy.ru

# Зависимости PHP (если vendor не копировали)
composer install --no-dev

# Симлинк для storage (без него /storage/... отдаёт 404)
php artisan storage:link

# Ассеты админки cherry-site (CSS, JS, шрифты) — если папку storage/ не копировали или она пустая
php artisan vendor:publish --tag=cherry-site:assets --force
php artisan vendor:publish --tag=cherry-site:upload --force

# Права на запись (пользователь веб-сервера должен писать в storage и bootstrap/cache)
chmod -R 775 storage bootstrap/cache
# Если нужно: chown -R www-data:www-data storage bootstrap/cache
```

**Если админка отдаёт 404 на все CSS/JS:** проверьте, что на сервере есть папка `storage/app/public/vendor/cherry-site/assets/` с файлами (css, js, fonts, img). Если её нет — выполните две команды `vendor:publish` выше и снова `php artisan storage:link`.

**Если в разделе «Новости» (и в других разделах) нет картинок:** картинки лежат в `storage/app/public/vendor/cherry-site/upload/`. При деплое через Git эта папка подтягивается при `git pull`. Если деплой без Git — скопируйте папку `upload` на сервер в то же место.

## 3. Настройка .env на сервере

Проверьте/задайте:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://s-pokoleniy.ru` (или ваш домен)
- `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` — данные БД на хостинге

## 4. Веб-сервер: корень сайта

**Document root** должен указывать на папку **`public`** проекта:

- Путь к сайту: `/путь/к/s-pokoleniy.ru/public`

Тогда запросы к `/storage/...` обрабатываются так: веб-сервер отдаёт файлы по симлинку `public/storage` и сам выставляет правильный Content-Type для CSS/JS. Отдельный роутер (`server.php`, `public/router.php`) на проде не нужен — они только для встроенного PHP-сервера локально.

### Apache

- Включён `mod_rewrite`.
- В `public/.htaccess` уже есть правила перенаправления в `index.php`.
- Для виртуального хоста: `DocumentRoot /путь/к/s-pokoleniy.ru/public`.

### Nginx

Пример для `location /`:

```nginx
root /путь/к/s-pokoleniy.ru/public;
index index.php;
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php7.2-fpm.sock;  # или 127.0.0.1:9000
    fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    include fastcgi_params;
}
```

## 5. Краткий чеклист

- [ ] Скопирован проект, на сервере выполнен `composer install --no-dev`
- [ ] Выполнен `php artisan storage:link`
- [ ] Настроены права на `storage` и `bootstrap/cache`
- [ ] В `.env` заданы продовые APP_URL, DB_*, APP_DEBUG=false
- [ ] Document root веб-сервера = `.../public`
- [ ] Миграции при необходимости: `php artisan migrate --force`

После этого сайт и админка (`/cherry-site/admin/login`) должны открываться; стили админки на проде отдаются веб-сервером из `public/storage` с корректным MIME.
