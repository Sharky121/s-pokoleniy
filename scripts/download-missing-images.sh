#!/bin/bash
# Скачивает недостающие картинки по списку из fill-missing-images.php --list.
# Запуск из корня проекта:
#   docker compose -p spokoleniy exec -T app php scripts/fill-missing-images.php --list 2>/dev/null | grep '|' | bash scripts/download-missing-images.sh
# Или сохранить список в файл и передать в скрипт:
#   docker compose -p spokoleniy exec -T app php scripts/fill-missing-images.php --list 2>/dev/null | grep '|' > scripts/missing-list.txt
#   bash scripts/download-missing-images.sh < scripts/missing-list.txt

BASE="$(cd "$(dirname "$0")/.." && pwd)"
count=0
while IFS='|' read -r rel url; do
  rel="${rel%%[[:space:]]*}"; url="${url##*[[:space:]]}"; url="${url%%[[:space:]]*}"
  [ -z "$rel" ] || [ -z "$url" ] && continue
  full="$BASE/$rel"
  dir="$(dirname "$full")"
  mkdir -p "$dir"
  if curl -sfL -o "$full" "$url" 2>/dev/null && [ -s "$full" ]; then
    echo "OK: $rel"
    ((count++)) || true
  else
    echo "Ошибка: $rel"
  fi
done
echo "Загружено: $count файлов."
