#!/bin/bash
# Скачивает тематические картинки из списка scripts/thematic-download-list.txt (path|url).
# Для Wikimedia использует Referer и браузерный User-Agent. При неудаче — placehold.co.
# Запуск из корня проекта: bash scripts/download-thematic-from-list.sh

BASE="$(cd "$(dirname "$0")/.." && pwd)"
LIST="$BASE/scripts/thematic-download-list.txt"
if [ ! -f "$LIST" ]; then
  echo "Сначала создайте список: php scripts/replace-covers-with-thematic.php"
  exit 1
fi

UA="Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36"
REF="https://commons.wikimedia.org/"
PLACEHOLD="https://placehold.co/800x600.jpg"

ok=0
err=0
while IFS= read -r line; do
  rel="${line%%|*}"; url="${line#*|}"; url="${url%%[[:space:]]*}"
  [ -z "$rel" ] || [ -z "$url" ] && continue
  full="$BASE/$rel"
  dir="$(dirname "$full")"
  mkdir -p "$dir"

  if [[ "$url" == *"wikimedia.org"* ]]; then
    curl -sfL --max-time 25 -A "$UA" -H "Referer: $REF" -o "$full" "$url" 2>/dev/null
  else
    curl -sfL --max-time 25 -A "$UA" -o "$full" "$url" 2>/dev/null
  fi

  sz=0
  [ -f "$full" ] && sz=$(stat -f%z "$full" 2>/dev/null || stat -c%s "$full" 2>/dev/null)
  if [ -n "$sz" ] && [ "$sz" -gt 5000 ]; then
    echo "OK: $rel"
    ((ok++)) || true
  else
    curl -sfL --max-time 15 -A "$UA" -o "$full" "$PLACEHOLD" 2>/dev/null
    if [ -s "$full" ]; then
      echo "OK (fallback): $rel"
      ((ok++)) || true
    else
      echo "Ошибка: $rel"
      ((err++)) || true
    fi
  fi
done < "$LIST"
echo "Загружено: $ok, ошибок: $err."
