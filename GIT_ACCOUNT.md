# Привязка репозитория к вашему аккаунту

Git уже инициализирован в этой папке. Чтобы привязать проект к вашему аккаунту (GitHub, GitLab или другой хостинг):

## 1. Укажите имя и email для коммитов (один раз в этом репозитории)

```bash
cd /Users/sharky/Projects/s-pokoleniy/s-pokoleniy.ru

git config user.name "Ваше Имя"
git config user.email "ваш@email.com"
```

## 2. Добавьте удалённый репозиторий

Создайте пустой репозиторий на GitHub/GitLab (например, `s-pokoleniy.ru` или `spokoleniy`), затем:

```bash
git remote add origin https://github.com/ВАШ_ЛОГИН/ИМЯ_РЕПОЗИТОРИЯ.git
```

Для SSH (если настроены ключи):

```bash
git remote add origin git@github.com:ВАШ_ЛОГИН/ИМЯ_РЕПОЗИТОРИЯ.git
```

## 3. Первый коммит и отправка

```bash
git add .
git commit -m "Initial commit: Laravel 6, Связь поколений"
git branch -M main
git push -u origin main
```

Папка `vendor/` и файл `.env` в коммиты не попадут (указаны в `.gitignore`).
