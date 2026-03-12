<?php
/**
 * Патч PackageManifest под формат Composer 2 (Undefined index: name).
 * Запускается после composer install (post-install-cmd).
 */
$file = __DIR__ . '/../vendor/laravel/framework/src/Illuminate/Foundation/PackageManifest.php';
if (!is_file($file)) {
    return;
}
$content = file_get_contents($file);
// Уже пропатчено
if (strpos($content, "isset(\$packages['packages'])") !== false) {
    return;
}
// 1) Поддержка формата Composer 2: packages + packages-dev
$content = str_replace(
    "\$packages = json_decode(\$this->files->get(\$path), true);\n        }\n\n        \$ignoreAll",
    "\$packages = json_decode(\$this->files->get(\$path), true);\n            if (isset(\$packages['packages'])) {\n                \$packages = array_merge(\n                    \$packages['packages'] ?? [],\n                    \$packages['packages-dev'] ?? []\n                );\n            }\n        }\n\n        \$ignoreAll",
    $content
);
// 2) Пропуск записей без ключа name
$content = str_replace(
    "\$this->write(collect(\$packages)->mapWithKeys(function (\$package) {",
    "\$this->write(collect(\$packages)->filter(function (\$package) {\n            return isset(\$package['name']);\n        })->mapWithKeys(function (\$package) {",
    $content
);
file_put_contents($file, $content);
