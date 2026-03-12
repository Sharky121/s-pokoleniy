<?php
/**
 * Патч PackageManifest под формат Composer 2 (Undefined index: name).
 * Запуск: php scripts/patch-package-manifest.php
 * Вызывать после распаковки vendor.zip на сервере или после composer install.
 */
$base = dirname(__DIR__);
$file = $base . '/vendor/laravel/framework/src/Illuminate/Foundation/PackageManifest.php';
if (!is_file($file)) {
    fwrite(STDERR, "PackageManifest.php не найден. Пропуск.\n");
    exit(0);
}
$content = file_get_contents($file);
if (strpos($content, "isset(\$packages['packages'])") !== false) {
    exit(0);
}
$changed = false;
// 1) Формат Composer 2: packages + packages-dev (гибкий пробел/перенос)
$r1 = '#\$packages\s*=\s*json_decode\(\$this->files->get\(\$path\),\s*true\);\s*\}\s*\n\s*\n\s*\$ignoreAll#';
$s1 = "\$packages = json_decode(\$this->files->get(\$path), true);\n            if (isset(\$packages['packages'])) {\n                \$packages = array_merge(\n                    \$packages['packages'] ?? [],\n                    \$packages['packages-dev'] ?? []\n                );\n            }\n        }\n\n        \$ignoreAll";
if (preg_match($r1, $content)) {
    $content = preg_replace($r1, $s1, $content);
    $changed = true;
}
// 2) Пропуск записей без ключа name
$r2 = '#\$this->write\(collect\(\$packages\)->mapWithKeys\(function\s*\(\$package\)\s*\{#';
$s2 = "\$this->write(collect(\$packages)->filter(function (\$package) {\n            return isset(\$package['name']);\n        })->mapWithKeys(function (\$package) {";
if (preg_match($r2, $content)) {
    $content = preg_replace($r2, $s2, $content);
    $changed = true;
}
if ($changed) {
    file_put_contents($file, $content);
    echo "PackageManifest.php пропатчен для Composer 2.\n";
} else {
    fwrite(STDERR, "Патч не применён (возможно, файл уже изменён или другая версия Laravel).\n");
}
