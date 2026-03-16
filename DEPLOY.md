# Деплой на сервер

## Вариант: распаковка vendor.zip (без composer на сервере)

Если вы выкладываете уже собранный `vendor` архивом:

```bash
# 1. Удалить старую папку vendor
rm -rf vendor

# 2. Распаковать архив (A = All, если спрашивает про замену)
unzip vendor.zip

# 3. Обязательно: патч под формат Composer 2 (исправляет "Undefined index: name")
php scripts/patch-package-manifest.php

# 4. Ключ и кэш
php artisan key:generate
php artisan config:cache
```

Без шага 3 при формате `installed.json` от Composer 2 будет ошибка в `php artisan key:generate`.

---

## Composer на сервере

**Не используйте** `composer.phar` из репозитория — он может быть повреждён при клонировании. На сервере используйте один из вариантов.

### Вариант 1: Composer установлен в системе

```bash
cd /home/p500271/www/s-pokoleniy.ru
composer install --no-dev --optimize-autoloader
```

### Вариант 2: Скачать Composer один раз в папку проекта

```bash
cd /home/p500271/www/s-pokoleniy.ru
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
php composer.phar install --no-dev --optimize-autoloader
```

Дальше можно вызывать `php composer.phar` при необходимости или удалить старый повреждённый `composer.phar` и использовать только что скачанный.

### Вариант 3: Composer в домашней директории (без прав в каталог сайта)

```bash
# Один раз
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=$HOME/bin
php -r "unlink('composer-setup.php');"
# В PATH добавить $HOME/bin

cd /home/p500271/www/s-pokoleniy.ru
composer install --no-dev --optimize-autoloader
```

После успешного `composer install`:

```bash
php artisan key:generate
# и при необходимости: права на storage, bootstrap/cache, .env
```

---

## Недостающие картинки (404 на сайте)

Если в логах браузера 404 для файлов вроде `MDA.jpg`, `1.jpeg`, `matushka_concert.jpg` и т.п., на сервере нет файлов в `storage/app/public/vendor/cherry-site/upload/`. Подставные картинки можно скачать так:

```bash
cd /home/p500271/www/s-pokoleniy.ru
bash scripts/download-missing-from-list.sh scripts/storage-image-paths.txt
```

Скрипт создаёт каталоги и качает по одному примерному изображению с Wikimedia Commons только для тех путей, где файла ещё нет. Для повторного деплоя можно запускать снова — существующие файлы не перезаписываются.
