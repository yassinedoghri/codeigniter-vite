<?php

declare(strict_types=1);

namespace CodeIgniterVite\Config;

use CodeIgniter\Config\BaseConfig;

class Vite extends BaseConfig
{
    public string $environment = 'production';

    public string $serverOrigin = 'http://localhost:5173';

    public string $resourcesDir = 'resources';

    public string $assetsDir = 'assets';

    public string $manifest = '.vite/manifest.json';

    public string $manifestCacheName = 'vite-manifest';

    /**
     * @var array<array{routes:list<string>,exclude?:list<string>,assets:list<string>}>
     */
    public array $routesAssets = [];
}
