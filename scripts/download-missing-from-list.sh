#!/bin/bash
# Скачивает картинки по списку только если файла ещё нет.
# Запуск из корня проекта:
#   bash scripts/download-missing-from-list.sh [файл_списка]
#   FORCE=1 bash scripts/download-missing-from-list.sh scripts/storage-image-paths.txt  # загрузить все, даже если файл есть

BASE="$(cd "$(dirname "$0")/.." && pwd)"
FORCE="${FORCE:-0}"
[ "$1" = "--force" ] && { FORCE=1; shift; }
LIST="${1:-$BASE/scripts/all-image-paths.txt}"
if [ ! -f "$LIST" ]; then
  echo "Файл списка не найден: $LIST"
  echo "Сгенерируйте: php scripts/fill-missing-images.php --list-all | grep '|' > scripts/all-image-paths.txt"
  echo "Или используйте: scripts/storage-image-paths.txt (только storage, для деплоя)"
  exit 1
fi

count=0
ok=0
while IFS= read -r line; do
  rel="${line%%|*}"; url="${line#*|}"; url="${url%%[[:space:]]*}"
  [ -z "$rel" ] || [ -z "$url" ] && continue
  full="$BASE/$rel"
  if [ "$FORCE" != "1" ] && [ -f "$full" ] && [ -s "$full" ]; then continue; fi
  ((count++)) || true
  dir="$(dirname "$full")"
  mkdir -p "$dir"
  if curl -sfL --max-time 30 -A "Mozilla/5.0 (compatible; SvyazPokolenij/1.0)" -o "$full" "$url" 2>/dev/null && [ -s "$full" ]; then
    echo "OK: $rel"
    ((ok++)) || true
  else
    echo "Ошибка: $rel"
  fi
done < "$LIST"
echo "Проверено отсутствующих: $count, загружено: $ok"
