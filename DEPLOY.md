# Деплой на сервер

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
