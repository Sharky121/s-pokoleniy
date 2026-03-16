#!/bin/bash
# Приводит public/images/art-*.jpg к размеру 800×600. ImageMagick или sips (macOS).
# Запуск из корня проекта: bash scripts/art-resize-covers.sh

set -e
BASE="$(cd "$(dirname "$0")/.." && pwd)"
cd "$BASE/public/images"
W=800
H=600

resize_with_convert() {
  local f="$1"
  local tmp="${f}.tmp"
  convert "$f" -resize "${W}x${H}^" -gravity center -extent "${W}x${H}" "$tmp" && mv -f "$tmp" "$f"
}

resize_with_sips() {
  local f="$1"
  sips -Z "$W" "$f" --out "$f" >/dev/null 2>&1
  sips --cropToHeightWidth "$H" "$W" "$f" >/dev/null 2>&1
  return 0
}

for f in art-*.jpg; do
  [ -f "$f" ] || continue
  if command -v convert >/dev/null 2>&1; then
    resize_with_convert "$f" && echo "OK: $f"
  elif command -v sips >/dev/null 2>&1; then
    resize_with_sips "$f" && echo "OK: $f"
  else
    echo "Установите ImageMagick (convert) или используйте macOS (sips)."
    exit 1
  fi
done
echo "Готово."
