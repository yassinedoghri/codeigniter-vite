<?php

declare(strict_types=1);

namespace CodeIgniterVite;

class ViteManifest
{
    /**
     * @var array<string, ViteManifestChunk>
     */
    public readonly array $chunks;

    public readonly bool $loadError;

    public function __construct(
        protected string $manifestPath
    ) {
        $manifestData = file_get_contents($this->manifestPath);

        if (! $manifestData) {
            $this->chunks = [];
            $this->loadError = true;
            return;
        }

        /** @var array<string,array{file:string,name?:string,src?:string,isEntry?:boolean,isDynamicEntry?:boolean,imports?:list<string>,dynamicImports?:list<string>,css?:list<string>}>|null $manifest */
        $manifest = @json_decode($manifestData, true);

        if ($manifest === null) {
            $this->chunks = [];
            $this->loadError = true;
            return;
        }

        $chunks = [];
        foreach ($manifest as $name => $chunkData) {
            $chunks[$name] = new ViteManifestChunk($chunkData);
        }

        $this->chunks = $chunks;
        $this->loadError = false;
    }

    public function renderAssetLinks(string $path, string $assetsDir = '', bool $isModulePreload = false): string
    {
        $html = '';
        if (array_key_exists($path, $this->chunks)) {
            $chunk = $this->chunks[$path];

            // import css dependencies if any
            foreach ($chunk->css as $cssFile) {
                $html .= render_asset_link($cssFile);
            }

            // import dependencies first for faster js loading
            foreach ($chunk->imports as $importPath) {
                $html .= $this->renderAssetLinks($importPath, $assetsDir, true);
            }

            $html .= render_asset_link($chunk->file, $assetsDir, $isModulePreload);
        }

        return $html;
    }
}
