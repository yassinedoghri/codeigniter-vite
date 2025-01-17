<?php

declare(strict_types=1);

namespace CodeIgniterVite;

class ViteManifestChunk
{
    public readonly string $file;

    public readonly ?string $name;

    public readonly ?string $src;

    public readonly bool $isEntry;

    public readonly bool $isDynamicEntry;

    /**
     * @var list<string>
     */
    public readonly array $imports;

    /**
     * @var list<string>
     */
    public readonly array $dynamicImports;

    /**
     * @var list<string>
     */
    public readonly array $css;

    /**
     * @param array{file:string,name?:string,src?:string,isEntry?:boolean,isDynamicEntry?:boolean,imports?:list<string>,dynamicImports?:list<string>,css?:list<string>} $chunkData
     */
    public function __construct(
        protected array $chunkData
    ) {
        $this->file = $chunkData['file'];
        $this->name = $chunkData['name'] ?? null;
        $this->src = $chunkData['src'] ?? null;
        $this->isEntry = $chunkData['isEntry'] ?? false;
        $this->isDynamicEntry = $chunkData['isDynamicEntry'] ?? false;
        $this->imports = $chunkData['imports'] ?? [];
        $this->dynamicImports = $chunkData['dynamicImports'] ?? [];
        $this->css = $chunkData['css'] ?? [];
    }
}
