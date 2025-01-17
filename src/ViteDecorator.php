<?php

declare(strict_types=1);

namespace CodeIgniterVite;

use CodeIgniter\View\ViewDecoratorInterface;
use CodeIgniterVite\Config\Vite as ViteConfig;

class ViteDecorator implements ViewDecoratorInterface
{
    public static function decorate(string $html): string
    {
        /** @var ViteConfig $viteConfig */
        $viteConfig = config('Vite');

        if ($viteConfig->routesAssets === []) {
            return $html;
        }

        $assetLinks = '';
        foreach ($viteConfig->routesAssets as $mapping) {
            foreach ($mapping['routes'] as $route) {
                if (url_is($route)) {
                    foreach ($mapping['assets'] as $asset) {
                        $assetLinks .= service('vite')
                            ->asset($asset);
                    }
                }
            }
        }

        return str_replace('</head>', $assetLinks . '</head>', $html);
    }
}
