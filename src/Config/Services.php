<?php

declare(strict_types=1);

namespace CodeIgniterVite\Config;

use CodeIgniter\Config\BaseService;
use CodeIgniterVite\Config\Vite as ViteConfig;
use CodeIgniterVite\Vite;

class Services extends BaseService
{
    public static function vite(bool $getShared = true): Vite
    {
        if ($getShared) {
            /** @var Vite */
            return static::getSharedInstance('vite');
        }

        /** @var ViteConfig $viteConfig */
        $viteConfig = config('Vite');

        return new Vite($viteConfig);
    }
}
