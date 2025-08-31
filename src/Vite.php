<?php

declare(strict_types=1);

namespace CodeIgniterVite;

use CodeIgniterVite\Config\Vite as ViteConfig;
use RuntimeException;

class Vite
{
    protected ?ViteManifest $manifest = null;

    public function __construct(
        protected ViteConfig $config
    ) {
        helper('vite');
    }

    public function asset(string $path): string
    {
        if ($this->config->environment !== 'production') {
            // get dev asset
            return render_asset_link($path, $this->config->serverOrigin);
        }

        // get production asset
        if (! $this->manifest instanceof ViteManifest) {
            /** @var ViteManifest|null $cachedManifest */
            $cachedManifest = cache($this->config->manifestCacheName);

            if ($cachedManifest === null) {
                $cachedManifest = new ViteManifest($this->config->manifest);
                cache()
                    ->save($this->config->manifestCacheName, $cachedManifest, DECADE);
            }

            $this->manifest = $cachedManifest;
        }

        if ($this->manifest->loadError) {
            throw new RuntimeException(
                "Could not load vite manifest: <strong>{$this->config->manifest}</strong> file not found! Forgot to run `vite build`?"
            );
        }

        return $this->manifest->renderAssetLinks($path);
    }
}
