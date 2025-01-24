<?php

declare(strict_types=1);

namespace CodeIgniterVite\Config;

use CodeIgniter\Config\BaseConfig;

class Vite extends BaseConfig
{
    public string $environment = 'production';

    public string $serverOrigin = 'http://localhost:5173';

    public string $resourcesDir = APPPATH . 'Resources';

    public string $assetsDir = 'assets';

    public string $manifest = '.vite/manifest.json';

    public string $manifestCacheName = 'vite-manifest';

    /**
     * @var array<array{routes:list<string>,exclude?:list<string>,assets:list<string>}>
     */
    public array $routesAssets = [];

    public function __construct()
    {
        parent::__construct();

        $this->environment = ((string) env('VITE_ENVIRONMENT') === '' ? $this->environment : (string) env(
            'VITE_ENVIRONMENT'
        ));
        $this->serverOrigin = ((string) env('VITE_SERVER_ORIGIN') === '' ? $this->serverOrigin : (string) env(
            'VITE_SERVER_ORIGIN'
        ));
        $this->resourcesDir = ((string) env('VITE_RESOURCES_DIR') === '' ? $this->resourcesDir : (string) env(
            'VITE_RESOURCES_DIR'
        ));
        $this->assetsDir = ((string) env('VITE_ASSETS_DIR') === '' ? $this->assetsDir : (string) env(
            'VITE_ASSETS_DIR'
        ));
        $this->manifest = ((string) env('VITE_MANIFEST') === '' ? $this->manifest : (string) env('VITE_MANIFEST'));
    }
}
