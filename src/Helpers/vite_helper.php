<?php

declare(strict_types=1);

function render_asset_link(string $file, string $base = '', bool $isModulePreload = false): string
{
    $assetUrl = implode('/', [$base, $file]);

    if ($isModulePreload) {
        return sprintf('<link rel="modulepreload" href="%s" />', $assetUrl);
    }

    return match (pathinfo($file, PATHINFO_EXTENSION)) {
        'css' => sprintf('<link rel="stylesheet" href="%s"/>', $assetUrl),
        'js','ts' => sprintf('<script type="module" src="%s"></script>', $assetUrl),
        default => '',
    };
}
